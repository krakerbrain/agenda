<?php

require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Schedules.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();

try {
    $company_id = $_SESSION['company_id'];
    $schedules = new Schedules($conn, $company_id);

    // Obtener los servicios y devolver el JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $schedules->getSchedules()]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
}
