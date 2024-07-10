<?php
require_once __DIR__ . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();

$data = json_decode(file_get_contents('php://input'), true);
$service_id = $data['service_id'];

try {
    $stmt = $conn->prepare("SELECT duration FROM services WHERE id = :service_id");
    $stmt->bindParam(':service_id', $service_id);
    $stmt->execute();
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($service) {
        echo json_encode(['duration' => (float)$service['duration']]);
    } else {
        echo json_encode(['message' => 'Servicio no encontrado']);
        http_response_code(404);
    }
} catch (PDOException $e) {
    echo json_encode(['message' => 'Error en la base de datos: ' . $e->getMessage()]);
    http_response_code(500);
} finally {
    $conn = null;
}
