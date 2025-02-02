<?php
// /public/register.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/twig.php';

// Check if a staff account already exists.
$stmt = $pdo->query("SELECT COUNT(*) FROM staff");
if ($stmt->fetchColumn() > 0) {
    // If a staff account exists, registration should not be accessible.
    header("Location: login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) {
        $error = "Please provide both username and password.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO staff (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $password_hash]);
        header("Location: login.php");
        exit;
    }
}

echo $twig->render('register.twig', ['error' => $error]);
