<?php
// /includes/db.php

$host    = 'localhost';
$db      = 'booking_app';
$user    = 'webdev';
$pass    = 'W3bD£velopment';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Instead of throwing an error, log it and return a JSON response
    error_log("Database Connection Failed: " . $e->getMessage());

    header("Content-Type: application/json");
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}
