<?php
// /api/get_services.php

header("Content-Type: application/json");
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/db.php';

// Check if the database connection was successful
if (!$pdo) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Get the date from the URL, or use today's date by default
$date = $_GET['date'] ?? date('Y-m-d');

// Ensure default services exist for the given date
function ensureServicesExist($pdo, $date) {
    $services = ['breakfast', 'lunch', 'dinner'];
    foreach ($services as $service) {
        // Check if a record exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM services WHERE service_date = ? AND service_type = ?");
        $stmt->execute([$date, $service]);
        $count = $stmt->fetchColumn();
        if ($count == 0) {
            // Insert default values
            $insert = $pdo->prepare("INSERT INTO services (service_date, service_type, available_seats, enabled) VALUES (?, ?, ?, ?)");
            if (!$insert->execute([$date, $service, 20, 1])) {
                error_log("Failed to insert default service: $service on $date");
            }
        }
    }
}

// Ensure services exist
ensureServicesExist($pdo, $date);

// Retrieve only enabled services
$stmt = $pdo->prepare("SELECT service_type FROM services WHERE service_date = ? AND enabled = 1");
$stmt->execute([$date]);
$services = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Debugging log (Check in Apache error log)
error_log("Services for date $date: " . print_r($services, true));

// If no services found, return a friendly JSON response
if (empty($services)) {
    echo json_encode(["error" => "No available services for the selected date"]);
    exit;
}

// Return the services as JSON
echo json_encode(["status" => "success", "services" => $services]);
exit;
