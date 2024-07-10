<?php
require_once __DIR__ . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();

try {
    // Recibir los datos del cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(['message' => 'Datos inválidos recibidos']);
        http_response_code(400);
        exit;
    }

    $company_id = $data['company_id'];
    $name = $data['name'];
    $phone = $data['phone'];
    $mail = $data['mail'];
    $date = $data['date'];
    $start_time = $data['start_time'];
    $end_time = $data['end_time'];
    $id_service = $data['id_service'];

    // Preparar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO appointments (company_id, name, phone, mail, date, start_time, end_time, id_service) VALUES (:company_id, :name, :phone, :mail, :date, :start_time, :end_time, :id_service)");

    // Enlazar los parámetros
    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':mail', $mail);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':id_service', $id_service);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Cita reservada exitosamente!']);
        http_response_code(200);
    } else {
        echo json_encode(['message' => 'Error al reservar la cita.']);
        http_response_code(500);
    }
} catch (PDOException $e) {
    echo json_encode(['message' => 'Error en la base de datos: ' . $e->getMessage()]);
    http_response_code(500);
} finally {
    // Cerrar la conexión
    $conn = null;
}
