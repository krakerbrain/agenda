<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();

$name = $_POST['name'];
$logo = null;

// Manejar la subida del logo
if (!empty($_FILES['logo']['name'])) {
    $logo = 'master_admin/uploads/' . basename($_FILES['logo']['name']);
    move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
}

// Generar un token aleatorio
$token = bin2hex(random_bytes(16));

try {
    $conn->beginTransaction();

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

    echo json_encode(['success' => true, 'token' => $token, 'company_id' => $company_id]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false]);
}
