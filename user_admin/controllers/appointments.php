<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/Appointments.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

try {
    $company_id = $datosUsuario['company_id'];
    $user_id = $datosUsuario['user_id'];
    $appointments = new Appointments;
    $userModel = new Users;
    $user_role = $userModel->get_user_role($user_id);
    $user_count = $userModel->count_company_users($company_id);
    $has_multiple_users = ($user_count > 1);

    // Manejo de solicitudes DELETE
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $result = $appointments->delete_appointment($data['id']);
            if ($result > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Cita eliminada correctamente']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'No se pudo eliminar la cita']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No se pudo eliminar la cita']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $status = isset($_GET['status']) ? $_GET['status'] : 'all';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // Número de registros por página
        $offset = ($page - 1) * $limit;

        $appointmentsData = $appointments->get_paginated_appointments(
            $company_id,
            $user_id,
            $user_role, // Pasamos el rol
            $status,
            $offset,
            $limit
        );

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $appointmentsData,
            'show_provider_column' => ($user_role == 2 && $has_multiple_users),
            'is_owner' => ($user_role == 2) // Flag útil para el frontend
        ]);
    } else {
        // Manejo de otros métodos (si es necesario)
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
