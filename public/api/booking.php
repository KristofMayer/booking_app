<?php
// /api/booking.php
require_once __DIR__ . '/../includes/db.php';

// Helper function for tables (if needed)
function calculateTablesRequired($partySize) {
    if ($partySize <= 2) return 1;
    if ($partySize <= 4) return 2;
    if ($partySize <= 6) return 3;
    return 0;
}

// Retrieve and sanitize input
$booking_date   = $_POST['booking_date'] ?? '';
$service_type   = $_POST['service_type'] ?? '';
$party_size     = intval($_POST['party_size'] ?? 0);
$customer_name  = trim($_POST['customer_name'] ?? '');
$customer_phone = trim($_POST['customer_phone'] ?? '');

if (!$booking_date || !$service_type || !$party_size || !$customer_name || !$customer_phone) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing required fields.']);
    exit;
}

// Check if the service exists and is enabled for the chosen date
$stmt = $pdo->prepare("SELECT available_seats, enabled FROM services WHERE service_date = ? AND service_type = ?");
$stmt->execute([$booking_date, $service_type]);
$service = $stmt->fetch();
if (!$service) {
    http_response_code(400);
    echo json_encode(['message' => 'Selected service is not available on that date.']);
    exit;
}
if ($service['enabled'] != 1) {
    http_response_code(400);
    echo json_encode(['message' => 'Selected service is disabled on that date.']);
    exit;
}

// Check remaining seats: available_seats minus the sum of booked party sizes
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

// Insert the booking
$insert = $pdo->prepare("INSERT INTO bookings (booking_date, service_type, party_size, customer_name, customer_phone) VALUES (?, ?, ?, ?, ?)");
$insert->execute([$booking_date, $service_type, $party_size, $customer_name, $customer_phone]);

echo json_encode(['message' => 'Your booking has been received!']);
