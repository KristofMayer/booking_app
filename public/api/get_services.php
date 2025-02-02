<?php
// /api/get_services.php

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../includes/db.php';

// Get the date from the URL, or use today's date by default
$date = $_GET['date'] ?? date('Y-m-d');

// Ensure default services exist for the given date
function ensureServicesExist($pdo, $date) {
    $services = ['breakfast', 'lunch', 'dinner'];
    foreach ($services as $service) {
        // Check if a record exists for the given date and service type
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM services WHERE service_date = ? AND service_type = ?");
        $stmt->execute([$date, $service]);
        $count = $stmt->fetchColumn();
        if ($count == 0) {
            // Insert default: enabled = 1 and available_seats = 20
            $insert = $pdo->prepare("INSERT INTO services (service_date, service_type, available_seats, enabled) VALUES (?, ?, ?, ?)");
            $insert->execute([$date, $service, 20, 1]);
        }
    }
}

// Call the function to ensure the services exist
ensureServicesExist($pdo, $date);

// Retrieve only the enabled services for the specified date
$stmt = $pdo->prepare("SELECT service_type FROM services WHERE service_date = ? AND enabled = 1");
$stmt->execute([$date]);
$services = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Log for debugging purposes (check your Apache error log)
error_log("Services for date $date: " . print_r($services, true));

// Return the services as JSON
echo json_encode($services);
