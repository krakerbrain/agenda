<?php

require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Services.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();

try {
    $company_id = $_SESSION['company_id'];
    $services = new Services($conn, $company_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Procesar la data del formulario
        $servicesData = $_POST; // Asume que estás enviando los datos como application/x-www-form-urlencoded
        $services->saveServices($servicesData);
        echo json_encode(['success' => true, 'message' => 'Servicios guardados exitosamente.']);
    } else {
        // Obtener los servicios y devolver el JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $services->getServices()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
}
