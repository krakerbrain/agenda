<?php

require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/EmailTemplate.php';
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

    // Separar el tiempo en inicio y fin
    list($start_time, $end_time) = explode(' - ', $data['time']);

    // Crear objetos DateTime a partir de las cadenas de tiempo
    $startDateTime = new DateTime($data['date'] . ' ' . $start_time);
    $endDateTime = new DateTime($data['date'] . ' ' . $end_time);

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
    $result = $appointments->add_appointment($appointmentData);

    // Comprobar si hubo un error
    if (isset($result['error'])) {
        throw new Exception('Error al reservar la cita: ' . $result['error']);
    }

    // Ejecutar la consulta si rowcount es mayor a 0
    if (is_array($result)) {
        // Obtener el email template y el logo
        $emailTemplateBuilder = new EmailTemplate();
        $emailContent = $emailTemplateBuilder->buildEmail($appointmentData, 'reserva');

        // Enviar mensaje de WhatsApp
        $wspStatusCode = sendWspReserva("registro_reserva", $appointmentData['phone'], $appointmentData['name'], $appointmentData['date'], $formattedStartTime, $emailContent['company_name'], $result['appointment_token']);

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
