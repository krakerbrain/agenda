<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();

$name = $_POST['name'];
$logo = null;



// Generar un token aleatorio
$token = bin2hex(random_bytes(16));

try {
    $conn->beginTransaction();
    // Manejar la subida del logo dentro de la transacción solo si hay un archivo para subir
    if (!empty($_FILES['logo']['name'])) {
        $upload_dir = dirname(__DIR__) . '/master_admin/uploads/';

        // Obtener la extensión del archivo
        $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);

        // Formatear el nombre del archivo
        $formatted_name = 'logo-' . preg_replace('/[^a-zA-Z0-9]/', '_', $name) . '-' . date('dmY') . '-' . uniqid() . '.' . $extension;

        $logo_path = $upload_dir . $formatted_name;

        if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path)) {
            throw new Exception('Error al subir el archivo.');
        }

        // Si la subida fue exitosa, guarda la ruta del logo en la variable $logo
        $logo = 'master_admin/uploads/' . $formatted_name;
    }

    // Insertar la nueva compañía con el token
    $sql = $conn->prepare("INSERT INTO companies (name, logo, is_active, token) VALUES (:name, :logo, 1, :token)");
    $sql->bindParam(':name', $name);
    $sql->bindParam(':logo', $logo);
    $sql->bindParam(':token', $token);
    $sql->execute();

    $company_id = $conn->lastInsertId();

    // Insertar los horarios de trabajo de la nueva compañía
    $days = [1, 2, 3, 4, 5, 6, 7]; // Lunes a Viernes

    foreach ($days as $day) {
        $work_start = null;
        $work_end = null;
        $break_start = null;
        $break_end = null;
        $is_enabled = 1;

        $sql = $conn->prepare("INSERT INTO company_schedules (company_id, day_id, work_start, work_end, break_start, break_end, is_enabled) VALUES (:company_id, :day_id, :work_start, :work_end, :break_start, :break_end, :is_enabled)");
        $sql->bindParam(':company_id', $company_id);
        $sql->bindParam(':day_id', $day);
        $sql->bindParam(':work_start', $work_start);
        $sql->bindParam(':work_end', $work_end);
        $sql->bindParam(':break_start', $break_start);
        $sql->bindParam(':break_end', $break_end);
        $sql->bindParam(':is_enabled', $is_enabled);
        $sql->execute();
    }

    $conn->commit();

    echo json_encode(['success' => true, 'company_id' => $company_id]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'error' => 'Error al agregar la empresa: ' . $e->getMessage()]);
}
