<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();


try {
    $company_id = $datosUsuario['company_id'];
    $users = new Users;
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? null;

    if ($requestMethod === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $result = $users->delete_user($data['id']);
            if ($result > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el usuario']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No se pudo eliminar el usuario']);
        }
    } else {

        if ($requestMethod === 'GET') {
            if ($action === 'getRoles') {
                // Obtener roles disponibles
                echo json_encode([
                    'success' => true,
                    'data' => $users->getAboutRoles()
                ]);
            } elseif ($action === 'getUserForEdit' && isset($_GET['id'])) {
                // Obtener datos de usuario para edición
                $userId = (int)$_GET['id'];

                // Validar permisos (opcional)
                if ($datosUsuario['role_id'] > 2) { // Ajusta según tus roles
                    throw new Exception('No tienes permisos para esta acción');
                }

                $userData = $users->getUserForEdit($userId, $company_id);

                if (!$userData) {
                    throw new Exception('Usuario no encontrado o no pertenece a esta compañía');
                }

                echo json_encode([
                    'success' => true,
                    'data' => $userData
                ]);
            } else {
                // Listado general de usuarios
                echo json_encode([
                    'success' => true,
                    'data' => $users->get_users($company_id)
                ]);
            }
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}