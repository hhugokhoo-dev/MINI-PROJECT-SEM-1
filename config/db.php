<?php
$host = '127.0.0.1';
$port = '3306';   // MAMP's default MySQL port — check Preferences > Ports if yours differs
$db   = 'mini';
$user = 'root';
$pass = 'root';   // MAMP's default root password

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}