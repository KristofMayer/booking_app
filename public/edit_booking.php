<?php
// /public/edit_booking.php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/twig.php';
require_once __DIR__ . '/../includes/jwt_helper.php';

$token = $_GET['token'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');
$userData = JwtHelper::validateToken($token);
if (!$userData) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: dashboard.php?token=" . urlencode($token) . "&date=" . urlencode($date));
    exit;
}

// Fetch the booking
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();
if (!$booking) {
    header("Location: dashboard.php?token=" . urlencode($token) . "&date=" . urlencode($date));
    exit;
}

// Handle form submission for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_type = $_POST['service_type'];
    $party_size = intval($_POST['party_size']);
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $update = $pdo->prepare("UPDATE bookings SET service_type = ?, party_size = ?, customer_name = ?, customer_phone = ? WHERE id = ?");
    $update->execute([$service_type, $party_size, $customer_name, $customer_phone, $id]);
    header("Location: dashboard.php?token=" . urlencode($token) . "&date=" . urlencode($date));
    exit;
}

echo $twig->render('edit_booking.twig', [
    'booking' => $booking,
    'token' => $token,
    'date' => $date,
]);
