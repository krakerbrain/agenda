<?php
date_default_timezone_set('UTC');
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Database.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/Integrations/GoogleIntegrationManager.php';
require_once dirname(__DIR__, 2) . '/classes/IntegrationManager.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';

$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
$company_id = $datosUsuario['company_id'];

$database = new Database();
$appointments = new Appointments($database);
$data = json_decode(file_get_contents('php://input'), true);
// Validación robusta
if ($data === null || !isset($data['id'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Datos JSON inválidos o falta el ID']));
}
$id = $data['id'];

try {
    // Iniciar una transacción
    $database->beginTransaction();

    // Obtener la cita desde la base de datos
    $appointment = $appointments->get_appointment($id);

    if (!$appointment) {
        throw new Exception('Cita no encontrada.');
    }

    $integrationManager = new IntegrationManager();
    $integrationData = $integrationManager->getGoogleCalendarIntegration($company_id);
    $eventId = null;

    if ($integrationData['enabled']) {
        // Crear evento en Google Calendar
        $googleIntegration = new GoogleIntegrationManager($company_id);
        $userTimeZone = $googleIntegration->getUserTimeZone();

        // Formatear la fecha y hora
        list($startDateTimeFormatted, $endDateTimeFormatted) = formatDateTime(
            $appointment['date'],
            $appointment['start_time'],
            $appointment['end_time'],
            $userTimeZone
        );

        // Crear el evento en Google Calendar
        $eventSummary = $appointment['service'] . " con " . $appointment['name'];
        $eventId = $googleIntegration->createEvent($eventSummary, $startDateTimeFormatted, $endDateTimeFormatted, $userTimeZone);
    }

    // Actualizar el ID del evento en la base de datos
    $updateResult = $appointments->updateAppointment($id, 1, $eventId);

    if (!$updateResult['success']) {
        throw new Exception('Error al actualizar la cita: ' . ($updateResult['message'] ?? ''));
    }

    if ($updateResult['rows_affected'] === 0) {
        throw new Exception('No se encontró la cita para actualizar');
    }

    // Confirmar la transacción
    header('Content-Type: application/json');
    $database->endTransaction();
    echo json_encode(['message' => 'Cita confirmada exitosamente', 'success' => true]);
    http_response_code(200);
} catch (Exception $e) {
    // Intentar obtener el mensaje de error
    $errorResponse = $e->getMessage();
    // Verificar si el mensaje contiene directamente "invalid_grant"
    if (strpos($errorResponse, 'invalid_grant') !== false) {
        $integrationManager->clearGoogleCalendarIntegration($company_id);
        echo json_encode([
            'error' => true,
            'code' => 401,
            'message' => 'Token inválido o expirado. Requiere reautenticación.',
        ]);
        http_response_code(401);
    } else {
        preg_match('/\{(?:[^{}]|(?R))*\}/', $errorResponse, $matches);

        if ($matches) {
            $errorData = json_decode($matches[0], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if (isset($errorData['error']['code']) && $errorData['error']['code'] == 401) {
                    echo json_encode([
                        'error' => true,
                        'code' => 401,
                        'message' => 'Error de autenticación: ' . $errorData['error']['message'],
                    ]);
                    http_response_code(401);
                } else {
                    echo json_encode([
                        'error' => true,
                        'code' => $errorData['error']['code'] ?? 500,
                        'message' => $errorData['error']['message'] ?? 'Error desconocido.',
                    ]);
                    http_response_code($errorData['error']['code'] ?? 500);
                }
            } else {
                echo json_encode([
                    'error' => true,
                    'code' => 500,
                    'message' => 'Error interno al procesar la respuesta de la API: ' . $e->getMessage(),
                ]);
                http_response_code(500);
            }
        } else {
            echo json_encode([
                'error' => true,
                'code' => 500,
                'message' => 'Error interno: ' . $e->getMessage(),
            ]);
            http_response_code(500);
        }
    }
} finally {
    $database = null;
}

/**
 * Formatea la fecha y hora en RFC3339
 *
 * @param string $date Fecha de la cita
 * @param string $startTime Hora de inicio
 * @param string $endTime Hora de fin
 * @param string $timeZone Zona horaria del usuario
 * @return array Formato de fecha y hora [inicio, fin]
 */
function formatDateTime($date, $startTime, $endTime, $timeZone = 'America/Santiago')
{
    $startDateTime = new DateTime("$date $startTime", new DateTimeZone($timeZone));
    $endDateTime = new DateTime("$date $endTime", new DateTimeZone($timeZone));

    $startDateTimeFormatted = $startDateTime->format(DateTime::RFC3339);
    $endDateTimeFormatted = $endDateTime->format(DateTime::RFC3339);

    return [$startDateTimeFormatted, $endDateTimeFormatted];
}
