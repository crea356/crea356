<?php
require 'confi.php';

// Example user (for testing)
$email = "test@example.com";
$password = "!!apple3!!"; // plain text password

$hashedPassword = password_hash('!!apple3!!', PASSWORD_DEFAULT);
try {
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
    $stmt->execute([
        'email' => $email,
        'password' => $hashedPassword
    ]);
    echo "User created successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}