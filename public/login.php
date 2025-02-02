<?php
// /public/login.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/twig.php';
require_once __DIR__ . '/../includes/jwt_helper.php';

// Check if there is at least one staff account.
// If there are none, redirect to the registration page.
$stmt = $pdo->query("SELECT COUNT(*) FROM staff");
if ($stmt->fetchColumn() == 0) {
    header("Location: register.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE username = ?");
        $stmt->execute([$username]);
        $staff = $stmt->fetch();
        if ($staff && password_verify($password, $staff['password_hash'])) {
            // Generate JWT token.
            $token = JwtHelper::generateToken(['staff_id' => $staff['id'], 'username' => $staff['username']]);
            header("Location: dashboard.php?token=" . urlencode($token));
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}

echo $twig->render('login.twig', ['error' => $error]);
