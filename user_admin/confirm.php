<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';
$manager = new DatabaseSessionManager();
// $manager->startSession();
// session_start();
$conn = $manager->getDB();

// Crear instancia de la clase Appointments
$appointments = new Appointments($conn);


require_once dirname(__DIR__) . '/google_services/google_client.php';
require_once dirname(__DIR__) . '/google_services/calendar_service.php';
include 'send_email.php';
include 'send_wsp.php';


$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

try {
    // Iniciar una transacción
    $conn->beginTransaction();

    // Obtener la cita desde la base de datos
    $appointment = $appointments->getAppointment($id);

    if (!$appointment) {
        throw new Exception('Cita no encontrada.');
    }

    // Configurar cliente de Google
    $client = getClient();

    $timeZone = getUserTimeZone($client);
    list($startDateTimeFormatted, $endDateTimeFormatted) = formatDateTime($appointment['date'], $appointment['start_time'], $appointment['end_time'], $timeZone);
    createCalendarEvent($client, $appointment['name'], $appointment['service'], $startDateTimeFormatted, $endDateTimeFormatted, $appointment['id'], $conn);

    $emailTemplateBuilder = new EmailTemplate();
    $emailContent = $emailTemplateBuilder->buildEmail($appointment['company_id'], 'confirmacion', $appointment['id_service'], $appointment['name'], $appointment['date'], $appointment['start_time']);
    // Enviar confirmación por correo electrónico
    sendEmail($appointment['mail'], $emailContent, 'Confirmación');

    // Enviar mensaje de WhatsApp
    $wspStatusCode = sendWspReserva("confirmar_reserva", $appointment['phone'], $appointment['name'], $appointment['date'], $appointment['start_time'], $emailContent['company_name'], $emailContent['social_token']);

    // Verificar si el mensaje de WhatsApp fue enviado correctamente
    if ($wspStatusCode == 200 || $wspStatusCode == 201) {
        // Confirmar la cita
        $appointments->confirmAppointment($id);

        // Confirmar la transacción
        $conn->commit();

        echo json_encode(['message' => 'Cita reservada y confirmación enviada exitosamente!', 'success' => true]);
        http_response_code(200);
    } else {
        // Si falla el envío de WhatsApp, revertir la transacción
        throw new Exception('Error al enviar el mensaje de WhatsApp. Código de estado: ' . $wspStatusCode);
    }
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollBack();
    echo json_encode(['message' => 'Error al confirmar la cita: ' . $e->getMessage()]);
    http_response_code(500);
} finally {
    $conn = null;
}
