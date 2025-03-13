<?php

require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/Customers.php';

// Crear instancia de la clase Appointments
$appointments = new Appointments();

// Crear instancia de la clase Customers
$customers = new Customers();

try {
    // Iniciar la transacción
    $appointments->beginTransaction();

    // Recibir los datos del cuerpo de la solicitud
    $data = $_POST;

    if (!$data) {
        throw new Exception('Datos inválidos recibidos');
    }

    // Convertir la duración del servicio a un número entero
    $serviceDuration = (int) $data['service_duration'];

    // Crear objeto DateTime para el inicio de la cita
    $startDateTime = new DateTime($data['date'] . ' ' . $data['time']);

    // Calcular el tiempo de finalización sumando la duración en minutos
    $endDateTime = clone $startDateTime; // Clonar para evitar modificar el original
    $endDateTime->modify("+{$serviceDuration} minutes");

    // Formatear el tiempo en "H:i" (horas:minutos)
    $formattedStartTime = $startDateTime->format('H:i');
    $formattedEndTime = $endDateTime->format('H:i');

    $phone = formatPhoneNumber($data['phone']);

    //Verificar si existe el cliente en la base de datos
    $customer_id = $customers->checkAndAssociateCustomer($phone, $data['mail'], $data['company_id']);
    if (!$customer_id) {
        $customerData = [
            'name' => $data['name'],
            'phone' => $phone,
            'mail' => $data['mail'],
            'company_id' => $data['company_id']
        ];
        $customer_id = $customers->add_customer($customerData);
    }

    // Preparar los datos para insertar la cita
    $appointmentData = [
        'company_id' => $data['company_id'],
        'customer_id' => $customer_id,
        'date' => $data['date'],
        'start_time' => $formattedStartTime,
        'end_time' => $formattedEndTime,
        'id_service' => $data['service']
    ];

    // Insertar la cita en la base de datos
    $result = $appointments->add_appointment($appointmentData);

    // Comprobar si hubo un error
    if (isset($result['error'])) {
        // Retornar el mensaje de error si la cita ya fue enviada
        if ($result['error'] === 'Cita ya ha sido enviada.') {
            echo json_encode(['message' => $result['error']]);
            http_response_code(400); // Bad Request
        } else {
            throw new Exception('Error al reservar la cita: ' . $result['error']);
        }
    } else {
        // Confirmar la transacción si todo fue exitoso
        $appointments->endTransaction();
        echo json_encode(['message' => 'Cita reservada exitosamente. Recibirás una confirmación en breve.']);
        http_response_code(200);
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
