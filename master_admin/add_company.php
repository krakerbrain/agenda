<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();

$name = $_POST['name'];
$logo = null;

// Manejar la subida del logo
if (!empty($_FILES['logo']['name'])) {
    $logo = '../master_admin/uploads/' . basename($_FILES['logo']['name']);
    move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
}

// Generar un token aleatorio
$token = bin2hex(random_bytes(16));

// Insertar la nueva compañía con el token
$sql = $conn->prepare("INSERT INTO companies (name, logo, is_active, token) VALUES (:name, :logo, 1, :token)");
$sql->bindParam(':name', $name);
$sql->bindParam(':logo', $logo);
$sql->bindParam(':token', $token);

if ($sql->execute()) {
    echo json_encode(['success' => true, 'token' => $token]);
} else {
    echo json_encode(['success' => false]);
}
