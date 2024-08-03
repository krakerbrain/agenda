<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();

try {
    // Recibir los datos del cuerpo de la solicitud
    $data = $_POST;
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
    $time = $data['time'];
    $id_service = $data['service'];

    // Separar el tiempo en inicio y fin
    list($start_time, $end_time) = explode(' - ', $time);

    // Crear objetos DateTime a partir de las cadenas de tiempo
    $startDateTime = new DateTime($date . ' ' . $start_time);
    $endDateTime = new DateTime($date . ' ' . $end_time);

    // Formatear el tiempo en "H:i" (horas:minutos)
    $formattedStartTime = $startDateTime->format('H:i');
    $formattedEndTime = $endDateTime->format('H:i');
    // Preparar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO appointments (company_id, name, phone, mail, date, start_time, end_time, id_service) VALUES (:company_id, :name, :phone, :mail, :date, :start_time, :end_time, :id_service)");

    // Enlazar los parámetros
    $stmt->bindParam(':company_id', $company_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':mail', $mail);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':start_time', $formattedStartTime);
    $stmt->bindParam(':end_time', $formattedEndTime);
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
