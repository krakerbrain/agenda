<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();

$input = json_decode(file_get_contents('php://input'), true);
$service_id = $input['service_id'];

$sql = $conn->prepare("SELECT * FROM service_categories WHERE service_id = :service_id");
$sql->bindParam(':service_id', $service_id);
$sql->execute();
$categories = $sql->fetchAll(PDO::FETCH_ASSOC);

if ($categories) {
    echo json_encode(['success' => true, 'categories' => $categories]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron categorías.']);
}
