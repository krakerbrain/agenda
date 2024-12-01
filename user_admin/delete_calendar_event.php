<?php
// Cargar dependencias solo una vez al inicio del archivo
require_once dirname(__DIR__) . '/google_services/google_client.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';
require_once dirname(__DIR__) . '/classes/Integrations/GoogleIntegrationManager.php';
require_once dirname(__DIR__) . '/classes/IntegrationManager.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';

// Inicializar variables y clases necesarias
$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
$company_id = $datosUsuario['company_id'];

// Obtener datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);
$appointmentId = $data['appointmentID'];
$eventId = isset($data['calendarEventID']) ? $data['calendarEventID'] : "";

// Crear una instancia de la clase de citas (Appointments)
$appointment = new Appointments();
$integrationManager = new IntegrationManager();

try {
    $integrationData = $integrationManager->getGoogleCalendarIntegration($company_id);
    // Inicializar la integración de Google solo si el eventId no está vacío
    if ($eventId != "" && $integrationData['enabled']) {
        $googleIntegration = new GoogleIntegrationManager($company_id);
        $googleIntegration->deleteEvent($eventId);
    }

    // Eliminar la cita de la base de datos
    $deletedRows = $appointment->delete_appointment($appointmentId);

    // Si se eliminó la cita correctamente, responder con éxito
    if ($deletedRows > 0) {
        echo json_encode(['success' => true, 'message' => 'Evento eliminado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró la cita para eliminar']);
    }
} catch (Exception $e) {
    // Intentar obtener el mensaje de error
    $errorResponse = $e->getMessage();
    // Intentar extraer el JSON del mensaje de error
    preg_match('/\{(?:[^{}]|(?R))*\}/', $errorResponse, $matches);

    if ($matches) {
        // Si encontramos un JSON válido, intentar decodificarlo
        $errorData = json_decode($matches[0], true);

        // Verificar si se pudo parsear el JSON correctamente
        if (json_last_error() === JSON_ERROR_NONE) {
            // Verificar si el error es de autenticación (código 401)
            if (isset($errorData['error']['code']) && $errorData['error']['code'] == 401) {
                echo json_encode([
                    'error' => true,
                    'code' => 401,
                    'message' => 'Error de autenticación: ' . $errorData['error']['message'],
                ]);
                http_response_code(401);
            } else {
                // Otros tipos de error, como autorización, conexión, etc.
                echo json_encode([
                    'error' => true,
                    'code' => $errorData['error']['code'] ?? 500,
                    'message' => $errorData['error']['message'] ?? 'Error desconocido.',
                ]);
                http_response_code($errorData['error']['code'] ?? 500);
            }
        } else {
            // Si no se puede parsear el mensaje JSON
            echo json_encode([
                'error' => true,
                'code' => 500,
                'message' => 'Error interno al procesar la respuesta de la API: ' . $e->getMessage(),
            ]);
            http_response_code(500);
        }
    } else {
        // Si no se encuentra un JSON en el mensaje de error
        echo json_encode([
            'error' => true,
            'code' => 500,
            'message' => 'Error interno: ' . $e->getMessage(),
        ]);
        http_response_code(500);
    }
}
