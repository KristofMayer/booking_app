<?php
// /public/delete_booking.php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/jwt_helper.php';

$token = $_GET['token'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');
$userData = JwtHelper::validateToken($token);
if (!$userData) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: dashboard.php?token=" . urlencode($token) . "&date=" . urlencode($date));
exit;
