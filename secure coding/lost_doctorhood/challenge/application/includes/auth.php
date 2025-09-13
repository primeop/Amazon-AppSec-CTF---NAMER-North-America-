<?php
require_once 'db.php';

function login($email, $password) {
    global $pdo;
    // Verify interdimensional credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND dimension = 'C-137'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Validate quantum signature (password)
    if ($user && (password_verify($password, $user['password']))) {
        // Initialize interdimensional session
        session_start();
        $_SESSION['user'] = $user['email'];
        $_SESSION['dimension'] = $user['dimension'];
        // Portal redirect to products
        header('Location: products.php');
        exit();
    }
    return false; // Invalid portal coordinates
}

function register($name, $email, $password) {
    global $pdo;
    // Generate quantum-encrypted password hash
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Register new Council member
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, dimension) VALUES (?, ?, ?, 'C-137')");
    return $stmt->execute([$name, $email, $hashedPassword]);
}
?>