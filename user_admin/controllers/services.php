<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/Services.php';
require_once dirname(__DIR__, 2) . '/classes/Schedules.php'; // Incluir la clase Schedules

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

try {
    $company_id = $datosUsuario['company_id'];
    $user_id = $datosUsuario['user_id'];
    $services = new Services($company_id, $user_id);
    $schedules = new Schedules($company_id, $user_id); // Instanciar la clase Schedules

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $servicesData = $_POST;

        $result = $services->saveServices($servicesData);
        $result = json_decode($result, true);

        if ($result['success'] && isset($result['new_service_ids'])) {
            // Asignar usuario solo a servicios nuevos
            foreach ($result['new_service_ids'] as $tempId => $realId) {
                $services->assignUserToService([
                    'service_id' => $realId,
                    'available_days' => implode(',', $servicesData['available_service_day'][$tempId] ?? []),
                    'is_active' => isset($servicesData['service_enabled'][$tempId]) ? 1 : 0
                ], true);
            }
        }

        // Respuesta uniforme (éxito/error lo maneja saveServices)
        header('Content-Type: application/json');
        echo json_encode($result);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        parse_str(file_get_contents("php://input"), $deleteData);

        if (isset($deleteData['service_id'])) {
            // Verificar si hay citas agendadas antes de eliminar un servicio
            $serviceId = $deleteData['service_id'];
            $hasAppointments = $services->checkAppointments($serviceId);

            if ($hasAppointments) {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar el servicio porque tiene citas agendadas.']);
            } else {
                $services->deleteService($serviceId);
                echo json_encode(['success' => true, 'message' => 'Servicio eliminado exitosamente.']);
            }
        } elseif (isset($deleteData['category_id'])) {
            $categoryId = $deleteData['category_id'];
            $services->deleteCategory($categoryId);
            echo json_encode(['success' => true, 'message' => 'Categoría eliminada exitosamente.']);
        }
    } else {
        // Obtener los servicios y los horarios
        $servicesData = $services->getServices();
        $schedulesData = $schedules->getSchedules(); // Obtener los horarios

        // Enviar ambos conjuntos de datos al frontend
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => [
                'services' => $servicesData,
                'schedules' => $schedulesData // Incluir horarios en la respuesta
            ]
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
