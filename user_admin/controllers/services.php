<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Services.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();

try {

    // Obtener el ID de la empresa (esto podría venir de una sesión, un parámetro GET/POST, etc.)
    $company_id = $_SESSION['company_id'];

    // Instanciar la clase Services
    $services = new Services($conn, $company_id);

    // Obtener los servicios y devolver el JSON
    header('Content-Type: application/json');
    //devolver un json con succes y ladata
    echo json_encode(['success' => true, 'data' => $services->getServices()]);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
