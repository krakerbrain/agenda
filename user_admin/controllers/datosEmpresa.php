<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/FileManager.php';
$manager = new DatabaseSessionManager();

$conn = $manager->getDB();

// Acceder a los datos de texto enviados por formData
$company_id = $_POST['company_id'] ?? null;
$name = $_POST['company_name'] ?? null;
$phone = $_POST['phone'] ?? null;
$address = $_POST['address'] ?? null;
$description = $_POST['description'] ?? null;
// Crear una instancia de FileManages
$fileManager = new FileManager();
try {
    $conn->beginTransaction();

    // Aquí manejarías la subida del archivo como en el ejemplo anterior
    $logo = !empty($_FILES['logo']['name']) ? $fileManager->uploadLogo($name, $company_id) : $_POST['logo_url'];

    // Actualizar los datos de la empresa
    $sql = $conn->prepare("UPDATE companies SET logo = :logo, phone = :phone, address = :address, description = :description WHERE id = :id");
    $sql->bindParam(':phone', $phone);
    $sql->bindParam(':address', $address);
    $sql->bindParam(':description', $description);
    $sql->bindParam(':logo', $logo);
    $sql->bindParam(':id', $company_id);
    $sql->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Datos de la empresa actualizados correctamente']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'error' => 'Error al agregar la empresa: ' . $e->getMessage()]);
}
