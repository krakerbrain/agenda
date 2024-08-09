<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
// $manager->startSession();
session_start();
$conn = $manager->getDB();

$data = json_decode(file_get_contents('php://input'), true);

$service_id = $data['id'];

// Actualizar días y horas de trabajo
$sql = $conn->prepare("DELETE FROM services WHERE id = :id AND company_id = :company_id");
$sql->bindParam(':id', $service_id);
$sql->bindParam(':company_id', $_SESSION['company_id']);
$sql->execute();

// Devolver respuesta

echo json_encode(['success' => true]);
