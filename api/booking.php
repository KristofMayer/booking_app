<?php
// /api/booking.php

require_once __DIR__ . '/../includes/db.php';

// Helper function to calculate the number of tables required based on party size.
function calculateTablesRequired($partySize) {
    if ($partySize <= 2) return 1;
    if ($partySize <= 4) return 2;
    if ($partySize <= 6) return 3;
    return 0; // Should not happen since party size is capped at 6.
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
    exit;
}

// Retrieve and sanitize input.
$booking_date   = $_POST['booking_date'] ?? '';
$service_type   = $_POST['service_type'] ?? '';
$party_size     = (int)($_POST['party_size'] ?? 0);
$customer_name  = trim($_POST['customer_name'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');

if (!$booking_date || !$service_type || !$party_size || !$customer_name || !$customer_phone) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing required fields.']);
    exit;
}

$tables_required = calculateTablesRequired($party_size);

// Check if the service (breakfast, lunch, dinner) is available on the selected date.
$stmt = $pdo->prepare("SELECT available_tables FROM services WHERE service_date = ? AND service_type = ?");
$stmt->execute([$booking_date, $service_type]);
$service = $stmt->fetch();
if (!$service) {
    http_response_code(400);
    echo json_encode(['message' => 'Selected service is not available on that date.']);
    exit;
}
$available_tables = (int)$service['available_tables'];

// Calculate already booked tables for this service on that date.
$stmt = $pdo->prepare("SELECT SUM(tables_required) AS booked_tables FROM bookings WHERE booking_date = ? AND service_type = ?");
$stmt->execute([$booking_date, $service_type]);
$result = $stmt->fetch();
$booked_tables = (int)($result['booked_tables'] ?? 0);

// Ensure there is enough capacity.
if (($booked_tables + $tables_required) > $available_tables) {
    http_response_code(400);
    echo json_encode(['message' => 'Not enough tables available for this service.']);
    exit;
}

// Insert the booking into the database.
$stmt = $pdo->prepare("INSERT INTO bookings (booking_date, service_type, party_size, tables_required, customer_name, customer_phone) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$booking_date, $service_type, $party_size, $tables_required, $customer_name, $customer_phone]);

echo json_encode(['message' => 'Your booking has been received!']);
