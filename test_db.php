<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
echo "Testing DB Connection...<br>";

$host = 'localhost';
$db   = 'task_m';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "SUCCESS: Connected to database '$db'.";
} catch(PDOException $e) {
    echo "FAILURE: Could not connect. Error: " . $e->getMessage();
}
?>
