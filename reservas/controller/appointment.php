<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__, 2) . '/user_admin/send_email.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();

try {
    // Iniciar la transacción
    $conn->beginTransaction();

    // Recibir los datos del cuerpo de la solicitud
    $data = $_POST;

    if (!$data) {
        throw new Exception('Datos inválidos recibidos');
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
        // Obtener el email template y el logo
        $emailTemplateBuilder = new EmailTemplate();
        $emailContent = $emailTemplateBuilder->buildEmail($company_id, 'reserva', $id_service, $name, $date, $formattedStartTime);
        // Enviar el correo
        sendEmail($mail, $emailContent, 'Reserva');

        // Confirmar la transacción
        $conn->commit();

        echo json_encode(['message' => 'Cita reservada exitosamente y correo enviado!']);
        http_response_code(200);
    } else {
        throw new Exception('Error al reservar la cita.');
    }
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
} finally {
    // Cerrar la conexión
    $conn = null;
}
