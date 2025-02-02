<?php
// /api/get_services.php

require_once __DIR__ . '/../includes/db.php';

// Get the date from the URL, or use today's date by default
$date = $_GET['date'] ?? date('Y-m-d');

// Ensure default services exist for the given date
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

// Retrieve only the enabled services for the specified date
$stmt = $pdo->prepare("SELECT service_type FROM services WHERE service_date = ? AND enabled = 1");
$stmt->execute([$date]);
$services = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($services);
