<?php
// /public/dashboard.php
ini_set('display_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/twig.php';
require_once __DIR__ . '/../includes/jwt_helper.php';

// Validate the JWT token (assume passed via GET for simplicity)
$token = $_GET['token'] ?? '';
$userData = JwtHelper::validateToken($token);
if (!$userData) {
    header("Location: login.php");
    exit;
}

// Get selected date from GET; default is today.
$date = $_GET['date'] ?? date('Y-m-d');

// Function to create default services if they don't exist
function ensureServicesExist($pdo, $date) {
    $services = ['breakfast', 'lunch', 'dinner'];
    foreach ($services as $service) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM services WHERE service_date = ? AND service_type = ?");
        $stmt->execute([$date, $service]);
        if ($stmt->fetchColumn() == 0) {
            // Insert default: enabled = 1 and available_seats = 20
            $insert = $pdo->prepare("INSERT INTO services (service_date, service_type, available_seats, enabled) VALUES (?, ?, ?, ?)");
            $insert->execute([$date, $service, 20, 1]);
        }
    }
}

ensureServicesExist($pdo, $date);

// Handle update of services if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_services'])) {
    $services = ['breakfast', 'lunch', 'dinner'];
    foreach ($services as $service) {
        $enabled = isset($_POST["{$service}_enabled"]) ? 1 : 0;
        $available_seats = intval($_POST["{$service}_seats"]);
        $update = $pdo->prepare("UPDATE services SET enabled = ?, available_seats = ? WHERE service_date = ? AND service_type = ?");
        $update->execute([$enabled, $available_seats, $date, $service]);
    }
    header("Location: dashboard.php?token=" . urlencode($token) . "&date=" . urlencode($date));
    exit;
}

// Handle adding a new booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_booking'])) {
    $booking_date = $date;
    $service_type = $_POST['service_type'];
    $party_size = intval($_POST['party_size']);
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    $insert = $pdo->prepare("INSERT INTO bookings (booking_date, service_type, party_size, customer_name, customer_phone) VALUES (?, ?, ?, ?, ?)");
    $insert->execute([$booking_date, $service_type, $party_size, $customer_name, $customer_phone]);
    header("Location: dashboard.php?token=" . urlencode($token) . "&date=" . urlencode($date));
    exit;
}

// Fetch service records for the selected date
$stmt = $pdo->prepare("SELECT * FROM services WHERE service_date = ?");
$stmt->execute([$date]);
$servicesData = [];
while ($row = $stmt->fetch()) {
    $servicesData[$row['service_type']] = $row;
}

// Fetch all bookings for the selected date
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_date = ?");
$stmt->execute([$date]);
$bookings = $stmt->fetchAll();

// Calculate remaining seats for each service:
// remaining = available_seats - SUM(party_size booked for that service)
$remainingSeats = [];
foreach (['breakfast', 'lunch', 'dinner'] as $service) {
    $remainingSeats[$service] = $servicesData[$service]['available_seats'];
}
foreach ($bookings as $booking) {
    $service = $booking['service_type'];
    $remainingSeats[$service] -= $booking['party_size'];
}

// Render the dashboard
echo $twig->render('dashboard.twig', [
    'username' => $userData['username'],
    'date' => $date,
    'services' => $servicesData,
    'remainingSeats' => $remainingSeats,
    'bookings' => $bookings,
    'token' => $token,
]);
