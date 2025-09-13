# Lost Doctorhood CTF Challenge - Security Writeup

## Challenge Overview
The "Lost Doctorhood" CTF challenge presents a web application with multiple critical security vulnerabilities that allow for SQL injection and file upload bypass attacks. The challenge simulates a system that has been compromised and requires security hardening.

## Vulnerabilities Identified

### 1. SQL Injection in ORDER BY Clause
**Location**: `challenge/application/includes/product.php` - `searchProducts()` function
**Severity**: Critical
**Description**: The `orderBy` parameter was passed through a weak `removeSQL()` function that only removed certain SQL keywords, allowing injection through the ORDER BY clause.

**Exploit**: The exploit.py attempts SQL injection with:
```python
p=["name+and+case+when+3933%3d3933+then+3933+else+json(char(107,86,85,107))+end"]
```

**Fix Applied**: Implemented whitelist validation for allowed columns:
```php
$allowedColumns = ['name', 'size'];
if (!in_array($orderBy, $allowedColumns)) {
    $orderBy = 'name'; // Default to safe column
}
```

### 2. File Upload Bypass Vulnerability
**Location**: `challenge/application/includes/product.php` - `uploadFile()` function
**Severity**: Critical
**Description**: The blacklist-based file validation could be bypassed using multiple techniques:
- Double extensions (e.g., `file.php.txt`)
- Case variations (e.g., `file.PhP`)
- Dangerous file types like `.so` and `.phar`

**Exploit**: The exploit.py attempts to upload:
1. A `.so` file (shared object)
2. A `.phar` file disguised as a ZIP

**Fix Applied**: 
- Replaced blacklist with whitelist of allowed extensions
- Added pattern detection for dangerous file types
- Implemented MIME type validation
- Added secure filename generation

### 3. Path Traversal Vulnerability
**Location**: `challenge/application/includes/product.php` - `uploadFile()` function
**Severity**: High
**Description**: Using only `basename()` was insufficient to prevent path traversal attacks.

**Fix Applied**: 
- Added regex filtering to remove dangerous characters
- Added validation for empty or dangerous filenames
- Implemented secure filename generation with `uniqid()`

## Security Improvements Implemented

### 1. Whitelist Validation
Replaced blacklist-based filtering with whitelist validation for both SQL columns and file extensions, making the system more secure by default.

### 2. Content Validation
Added MIME type checking using `finfo_file()` to validate actual file content, not just file extensions.

### 3. Enhanced Input Sanitization
- Proper filename sanitization with regex filtering
- Secure filename generation to prevent conflicts
- Pattern detection for dangerous file types

### 4. Defense in Depth
Multiple layers of validation:
- Extension whitelist
- MIME type validation
- Pattern detection
- Secure filename generation

## Code Changes Summary

The main security fixes were implemented in `challenge/application/includes/product.php`:

1. **SQL Injection Prevention**: Added whitelist validation for ORDER BY columns
2. **File Upload Security**: Complete rewrite of the `uploadFile()` function with:
   - Whitelist of allowed extensions
   - MIME type validation
   - Pattern detection for dangerous files
   - Secure filename generation
   - Enhanced path traversal protection

## Testing Results

The original exploit.py attempts both attack vectors:
1. SQL injection through the `order` parameter in `products.php`
2. File upload bypass through `idea.php`

Both attack vectors are now properly mitigated with the implemented security fixes.

## Conclusion

The "Lost Doctorhood" challenge demonstrated common web application vulnerabilities including SQL injection and file upload bypasses. The implemented fixes follow security best practices:

- **Whitelist over blacklist**: More secure by default
- **Input validation**: Multiple layers of validation
- **Content verification**: Check actual file content, not just extensions
- **Secure defaults**: Fail securely when validation fails

The system is now hardened against the identified attack vectors while maintaining functionality.

