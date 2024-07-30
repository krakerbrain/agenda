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
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        parse_str(file_get_contents("php://input"), $deleteData);

        if (isset($deleteData['service_id'])) {
            // Verificar si hay citas agendadas antes de eliminar un servicio
            $serviceId = $deleteData['service_id'];
            $hasAppointments = $services->checkAppointments($serviceId);

            if ($hasAppointments) {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar el servicio porque tiene citas agendadas.']);
            } else {
                $services->deleteService($serviceId);
                echo json_encode(['success' => true, 'message' => 'Servicio eliminado exitosamente.']);
            }
        } elseif (isset($deleteData['category_id'])) {
            $categoryId = $deleteData['category_id'];
            $services->deleteCategory($categoryId);
            echo json_encode(['success' => true, 'message' => 'Categoría eliminada exitosamente.']);
        }
    } else {
        // Obtener los servicios y devolver el JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $services->getServices()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
}
