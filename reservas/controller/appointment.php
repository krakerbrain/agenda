<?php
// require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__, 2) . '/user_admin/send_email.php';
require_once dirname(__DIR__, 2) . '/user_admin/send_wsp.php';

// Crear instancia de la clase Appointments
$appointments = new Appointments();


try {
    // Iniciar la transacción
    $appointments->beginTransaction();

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

    $phone = formatPhoneNumber($data['phone']);

    // Preparar los datos para insertar la cita
    $appointmentData = [
        'company_id' => $data['company_id'],
        'name' => $data['name'],
        'phone' => $phone,
        'mail' => $data['mail'],
        'date' => $data['date'],
        'start_time' => $formattedStartTime,
        'end_time' => $formattedEndTime,
        'id_service' => $data['service']
    ];

    // Insertar la cita en la base de datos
    $stmt = $appointments->add_appointment($appointmentData);

    // Ejecutar la consulta si rowcount es mayor a 0
    if ($stmt > 0) {
        // Obtener el email template y el logo
        $emailTemplateBuilder = new EmailTemplate();
        $emailContent = $emailTemplateBuilder->buildEmail($company_id, 'reserva', $id_service, $name, $date, $formattedStartTime);
        // CORREO DE RESERVA
        sendEmail($mail, $emailContent, 'Reserva');

        // CORREO ALERTA DE RESERVA
        $alertEmailContent = $emailTemplateBuilder->buildAppointmentAlert($company_id, $name, $date, $formattedStartTime);
        sendEmail(null, $alertEmailContent, null);

        // Enviar mensaje de WhatsApp
        $wspStatusCode = sendWspReserva("registro_reserva", $phone, $name, $date, $formattedStartTime, $emailContent['company_name'], $emailContent['social_token']);
        //Para pruebas
        // $wspStatusCode = 200;
        // Verificar si el mensaje de WhatsApp fue enviado correctamente
        if ($wspStatusCode == 200 || $wspStatusCode == 201) {
            // Confirmar la transacción si todo fue exitoso
            $appointments->endTransaction();

            echo json_encode(['message' => 'Cita reservada exitosamente y aviso enviado!']);
            http_response_code(200);
        } else {
            // Si falla el envío de WhatsApp, revertir la transacción
            throw new Exception('Error al enviar el mensaje de WhatsApp. Código de estado: ' . $wspStatusCode);
        }
    } else {
        throw new Exception('Error al reservar la cita.');
    }
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $appointments->cancelTransaction();
    echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
} finally {
    // Cerrar la conexión
    $appointments = null;
}

function formatPhoneNumber($telefono)
{
    // Eliminar espacios en blanco, guiones, paréntesis y el símbolo "+"
    $telefono = preg_replace('/[\s\-\(\)\+]/', '', $telefono);

    // Si el número empieza con "9" y tiene 8 dígitos (número móvil chileno), agregar "56" al inicio
    if (preg_match('/^9\d{8}$/', $telefono)) {
        return '56' . $telefono;
    }

    // Si el número ya empieza con "56" y tiene 11 dígitos, es correcto
    if (preg_match('/^56\d{9}$/', $telefono)) {
        return $telefono;
    }

    // Si el número ya empieza con "6" y tiene 9 dígitos (número fijo chileno), agregar "56" al inicio
    if (preg_match('/^\d{8}$/', $telefono)) {
        return '569' . $telefono;
    }

    // Si el número no es válido, lanzar una excepción
    throw new Exception('Número de teléfono inválido.');
}
