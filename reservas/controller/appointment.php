<?php

require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Database.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/Customers.php';
require_once dirname(__DIR__, 2) . '/classes/EmailTemplate.php';

$database = new Database();
// Crear instancia de la clase Appointments
$appointments = new Appointments($database);

// Crear instancia de la clase Customers
$customers = new Customers();

try {
    // Iniciar la transacción
    $database->beginTransaction();

    // Verificar si se recibieron datos POST
    if (empty($_POST)) {
        throw new Exception('No se recibieron datos.');
    }

    // Recibir los datos del cuerpo de la solicitud
    $data = $_POST;

    // Validar campos requeridos
    $requiredFields = ['date', 'time', 'service_duration', 'phone', 'mail', 'company_id', 'service', 'name', 'provider'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("El campo '$field' es requerido.");
        }
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

    // Verificar si el cliente ya existe o si esta bloqueado
    $customer_id = $customers->checkAndAssociateCustomer($data['name'], $phone, $data['mail'], $data['company_id']);

    if (is_array($customer_id) && isset($customer_id['error'])) {
        // Si hay un error, devolver el mensaje de error
        if ($customer_id['error'] === 'blocked') {
            // Manejar el caso de cliente bloqueado
            echo json_encode(['success' => false, 'message' => $customer_id['message']]);
            http_response_code(403); // Forbidden (o 400 si prefieres mantener Bad Request)

            // Enviar un correo electrónico (aquí iría la lógica para enviar el correo)
            $emailTemplate = new EmailTemplate();
            $emailTemplate->buildBlockedUserAlertMail($data);
        } else {
            // Manejar otros errores (si los hay en el futuro)
            echo json_encode(['success' => false, 'message' => $customer_id['message']]);
            http_response_code(400); // Bad Request
        }
        exit;
    } elseif (!$customer_id) {
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
        'user_id' => $data['provider'],
        'customer_id' => $customer_id,
        'date' => $data['date'],
        'start_time' => $formattedStartTime,
        'end_time' => $formattedEndTime,
        'id_service' => $data['service'],
        'service_category_id' => isset($data['category']) && !empty($data['category']) ? $data['category'] : null
    ];

    // Insertar la cita en la base de datos
    $result = $appointments->add_appointment($appointmentData);

    // Comprobar si hubo un error
    if (isset($result['error'])) {
        // Retornar el mensaje de error si la cita ya fue enviada
        if ($result['error'] === 'Cita ya ha sido enviada.') {
            echo json_encode(['success' => false, 'message' => $result['error']]);
            http_response_code(400); // Bad Request
        } else {
            throw new Exception('Error al reservar la cita: ' . $result['error']);
        }
    } else {
        // Confirmar la transacción si todo fue exitoso
        $database->endTransaction();
        echo json_encode(['success' => true, 'message' => 'Cita reservada exitosamente. Recibirás una confirmación en breve.']);
        http_response_code(200); // OK
    }
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $database->cancelTransaction();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500); // Internal Server Error
} finally {
    // Cerrar la conexión
    $database = null;
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
