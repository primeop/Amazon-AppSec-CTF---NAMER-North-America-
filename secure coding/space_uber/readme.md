# CTF Writeup: Space Uber SSRF Protection Challenge

## Challenge Overview
**Challenge**: Space Uber - SSRF Protection  
**Stage**: 4  
**Vulnerability**: Server-Side Request Forgery (SSRF) via `free_ride` action persistence  
**Objective**: Implement SSRF protection to prevent `free_ride` actions from persisting

## Vulnerability Analysis

### Initial Setup
The challenge consists of a Space Uber application with the following structure:
- **Frontend**: `challenge/app/` - User interface and request handling
- **API**: `challenge/api/` - Backend API for ride management
- **Exploit**: `exploit.py` - Proof of concept demonstrating the vulnerability

### Vulnerability Details

#### 1. **Root Cause**
The `free_ride` action in the API was persisting rides permanently to the filesystem, violating SSRF protection principles.

#### 2. **Attack Vector**
```python
# exploit.py - Demonstrating the vulnerability
import requests

BASE_URL = 'http://127.0.0.1'
sess = requests.Session()

# Step 1: Create a free ride (should not persist)
params = {
    'action': 'free_ride', 
    'dim1': 'Fantasy', 
    'dim2': 'Cronenberg'
}
r = sess.post(f'{BASE_URL}/conn.php', data=params)
print(r.text)

# Step 2: Verify persistence (vulnerability)
params = {
    'action': 'rides',
    'dim1': 'test',
    'dim2': 'test'
}
r = sess.post(f'{BASE_URL}/conn.php', data=params)
print(r.text)  # Shows the free ride persisted!
```

#### 3. **Vulnerable Code Flow**
1. User sends POST request to `conn.php` with `action=free_ride`
2. `conn.php` forwards request to API at `http://localhost:8000/free_ride/dim1/dim2`
3. API creates ride with price 0 and **saves to `booked_rides.json`**
4. Free ride persists across sessions and can be retrieved via `rides` action

#### 4. **API Vulnerability (challenge/api/index.php)**
```php
} elseif ($uri[0] === "free_ride" && count($uri) == 3) {
    if (isset($dimensions[$dim1]) && isset($dimensions[$dim2])) {
        $rideId = uniqid("ride_");
        $bookedRides[$rideId] = [
            "from" => $dim1,
            "to" => $dim2,
            "price" => 0
        ];
        file_put_contents($ridesFile, json_encode($bookedRides)); // VULNERABILITY: Persists!
        
        echo json_encode(["ride_id" => $rideId, "from" => $dim1, "to" => $dim2, "price" => $price]);
    }
}
```

## Solution Implementation

### Constraint
- **CTF Rule**: Only modifications to `challenge/app/` folder allowed
- **Cannot modify**: `challenge/api/` folder

### Protection Strategy
Implement SSRF protection at the application layer in `conn.php` to block `free_ride` actions before they reach the API.

### Code Fix

#### Before (Vulnerable):
```php
// challenge/app/conn.php
$action = $_POST['action'] ?? '';
$dim1 = urlencode($_POST['dim1'] ?? '');
$dim2 = urlencode($_POST['dim2'] ?? '');

if (!$action || !$dim1 || !$dim2) {
    echo json_encode(["error" => "Missing required parameters"]);
    exit;
}

$apiBaseUrl = 'http://localhost:8000';
$apiUrl = "$apiBaseUrl/$action/$dim1/$dim2";
// ... forwards all actions to API
```

#### After (Protected):
```php
// challenge/app/conn.php
$action = $_POST['action'] ?? '';
$dim1 = urlencode($_POST['dim1'] ?? '');
$dim2 = urlencode($_POST['dim2'] ?? '');

if (!$action || !$dim1 || !$dim2) {
    echo json_encode(["error" => "Missing required parameters"]);
    exit;
}

// SSRF Protection: Block free_ride action to prevent persistence
if ($action === 'free_ride') {
    echo json_encode(["error" => "Free ride action is not allowed"]);
    exit;
}

$apiBaseUrl = 'http://localhost:8000';
$apiUrl = "$apiBaseUrl/$action/$dim1/$dim2";
// ... only allows safe actions to reach API
```

## Verification

### Test 1: Blocked Free Ride
```bash
curl -X POST http://127.0.0.1/conn.php \
  -d "action=free_ride&dim1=Fantasy&dim2=Cronenberg"
```
**Expected Response**: `{"error":"Free ride action is not allowed"}`

### Test 2: Normal Actions Still Work
```bash
curl -X POST http://127.0.0.1/conn.php \
  -d "action=price&dim1=Fantasy&dim2=Cronenberg"
```
**Expected Response**: `{"from":"Fantasy","to":"Cronenberg","price":"1234 Schmeckles"}`

### Test 3: No Persistence
```bash
curl -X POST http://127.0.0.1/conn.php \
  -d "action=rides&dim1=test&dim2=test"
```
**Expected Response**: `[]` (empty array, no free rides persisted)

## Security Impact

### Before Fix
- ❌ Free rides persisted permanently
- ❌ SSRF vulnerability allowed data persistence
- ❌ Violated security principle of temporary actions

### After Fix
- ✅ Free rides blocked at application layer
- ✅ No persistence of unauthorized actions
- ✅ SSRF protection implemented
- ✅ Normal functionality preserved

## Key Learnings

1. **Defense in Depth**: Implement protection at multiple layers
2. **Input Validation**: Block dangerous actions before processing
3. **Principle of Least Privilege**: Only allow necessary actions
4. **CTF Constraints**: Work within given limitations creatively

## Files Modified
- `challenge/app/conn.php` - Added SSRF protection

## Files Analyzed
- `challenge/api/index.php` - Identified vulnerability source
- `exploit.py` - Understood attack vector
- `challenge/app/index.php` - Frontend interface
- `challenge/app/confirm.php` - Confirmation page
- `challenge/app/view.php` - File viewer

---
**Challenge Status**: ✅ **RESOLVED**  
**Protection Level**: ✅ **IMPLEMENTED**  
**Vulnerability**: ✅ **PATCHED**

