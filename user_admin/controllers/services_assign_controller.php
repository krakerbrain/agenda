<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Services.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';
require_once dirname(__DIR__, 2) . '/classes/Schedules.php'; // Incluir la clase Schedules

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
header('Content-Type: application/json');

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $users = new Users();
    $user_id = $_GET['user_id'] ?? $datosUsuario['user_id'];
    switch ($method) {
        case 'GET':
            $action = $_GET['action'] ?? '';
            $services = new Services($datosUsuario['company_id'], $user_id);

            if ($action === 'get_users') {
                // Obtener solo los usuarios activos que pueden tener servicios asignados
                $usersList = $users->get_all_users($datosUsuario['company_id']);

                echo json_encode([
                    'status' => 'success',
                    'users' => $usersList
                ]);
            } elseif ($action === 'get_services') {
                $schedules = new Schedules($datosUsuario['company_id'], $user_id);

                // 1. Obtener días habilitados por la compañía (configuración global)
                $companyAvailableDays = $schedules->getCompanyAvailableDays();

                // 2. Obtener días de trabajo del usuario específico
                $userWorkingDays = $schedules->getUserWorkingDays();

                // 3. Obtener servicios con disponibilidad combinada
                $servicesData = $services->getServicesWithUserAvailability(
                    $companyAvailableDays,
                    $userWorkingDays
                );

                echo json_encode([
                    'status' => 'success',
                    'data' => $servicesData
                ]);
            } else {
                throw new Exception('Acción no válida');
            }
            break;
        case 'POST':
            // 1. Obtener los datos del cuerpo de la solicitud
            $data = json_decode(file_get_contents('php://input'), true);
            $services = new Services($datosUsuario['company_id'], $data['user_id']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
            }
            if (isset($data['action']) && $data['action'] === 'save_assignments') {
                $user_id = $data['user_id'];
                $assignments = $data['assignments'] ?? [];

                $result = $services->saveUserAssignments($assignments);

                if ($result) {
                    echo json_encode(['status' => 'success', 'message' => 'Asignaciones guardadas correctamente']);
                } else {
                    throw new Exception('Error al guardar las asignaciones');
                }
            }
            break;
        default:
            throw new Exception('Método no permitido');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
