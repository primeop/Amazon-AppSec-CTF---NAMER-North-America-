<?php
require_once 'db.php';

function searchProducts($name = '', $orderBy = 'name') {
    global $pdo;
    
    // Whitelist allowed order by columns to prevent SQL injection
    $allowedColumns = ['name', 'size'];
    if (!in_array($orderBy, $allowedColumns)) {
        $orderBy = 'name'; // Default to safe column
    }
    
    $query = "SELECT * FROM products WHERE name LIKE :name AND dimension = 'C-137' ORDER BY $orderBy";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['name' => "%$name%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function removeSQL($string) {
    // This function is now deprecated - use whitelist validation instead
    $string = preg_replace('/\b(SELECT|FROM|INSERT|UPDATE|DELETE|WHERE|AND|OR|NOT|LIKE)\b/', '', $string);
    $string = trim($string);
    return $string;
}

function uploadFile($file) {
    $directory = '../uploads/';

    // Create a portal to the uploads dimension
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    // Secure filename handling - prevent path traversal
    $originalName = $file['name'];
    $file['name'] = basename($file['name']);
    
    // Additional path traversal protection
    $file['name'] = preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    
    // Prevent empty or dangerous filenames
    if (empty($file['name']) || $file['name'] === '.' || $file['name'] === '..') {
        return false;
    }
    
    // Whitelist of allowed file extensions (more secure than blacklist)
    $allowedExtensions = ['.txt', '.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png', '.gif'];
    $fileExtension = strtolower(strrchr($file['name'], '.'));
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        return false; // File type not allowed by the Council of Ricks
    }
    
    // Check for double extensions and other bypass attempts
    $nameWithoutExt = strtolower(substr($file['name'], 0, strrpos($file['name'], '.')));
    $dangerousPatterns = ['php', 'phtml', 'htaccess', 'sh', 'py', 'pl', 'cgi', 'xml', 'htm', 'tar', 'rar', 'so', 'phar'];
    
    foreach ($dangerousPatterns as $pattern) {
        if (strpos($nameWithoutExt, $pattern) !== false) {
            return false; // Suspicious file detected
        }
    }
    
    // Validate file content using MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimeTypes = [
        'text/plain',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/gif'
    ];
    
    if (!in_array($mimeType, $allowedMimeTypes)) {
        return false; // Invalid file content detected
    }

    // Check if invention blueprint exceeds quantum storage limit (1MB)
    if ($file['size'] > 1000000) {
        return false; // Exceeds interdimensional bandwidth limits
    }

    // Generate secure filename to prevent conflicts and attacks
    $secureFilename = uniqid('invention_', true) . $fileExtension;
    
    // Transport the invention blueprint through the portal
    if (move_uploaded_file($file['tmp_name'], $directory . $secureFilename)) {
        return true; // Invention successfully cataloged
    }
    
    return false; // Upload failed
}
?>