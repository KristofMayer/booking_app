<?php
// /api/booking.php

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/db.php';

// Helper function to calculate tables required (if needed)
// function calculateTablesRequired($partySize) { ... }

$booking_date   = $_POST['booking_date'] ?? '';
$service_type   = $_POST['service_type'] ?? '';
$party_size     = intval($_POST['party_size'] ?? 0);
$customer_name  = trim($_POST['customer_name'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');

// Validate required fields
if (!$booking_date || !$service_type || !$party_size || !$customer_name || !$customer_phone) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing required fields.']);
    exit;
}

// Query the service record for that date and service type
$stmt = $pdo->prepare("SELECT available_seats, enabled FROM services WHERE service_date = ? AND service_type = ?");
$stmt->execute([$booking_date, $service_type]);
$service = $stmt->fetch();

if (!$service) {
    http_response_code(400);
    echo json_encode(['message' => 'Selected service is not available on that date.']);
    exit;
}

// Check if the service is enabled
if ($service['enabled'] != 1) {
    http_response_code(400);
    echo json_encode(['message' => 'Selected service is disabled on that date.']);
    exit;
}

// Calculate remaining seats: available seats minus already booked seats
$stmt = $pdo->prepare("SELECT SUM(party_size) as booked FROM bookings WHERE booking_date = ? AND service_type = ?");
$stmt->execute([$booking_date, $service_type]);
$result = $stmt->fetch();
$booked = intval($result['booked'] ?? 0);
$remaining = $service['available_seats'] - $booked;

if ($remaining < $party_size) {
    http_response_code(400);
    echo json_encode(['message' => 'Not enough seats available for this service.']);
    exit;
}

// Insert the booking if all validations pass
$insert = $pdo->prepare("INSERT INTO bookings (booking_date, service_type, party_size, customer_name, customer_phone) VALUES (?, ?, ?, ?, ?)");
$insert->execute([$booking_date, $service_type, $party_size, $customer_name, $customer_phone]);

echo json_encode(['message' => 'Your booking has been received!']);
