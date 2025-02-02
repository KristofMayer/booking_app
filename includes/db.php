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
    // In production, log errors instead of echoing them.
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
