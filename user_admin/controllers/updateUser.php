<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/FileManager.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';

header('Content-Type: application/json');

try {
    $auth = new JWTAuth();
    $datosUsuario = $auth->validarTokenUsuario();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido',
            'errors' => ['method' => 'Solo se permiten solicitudes POST']
        ]);
        exit;
    }

    $userId = $_POST['user_id'] ?? null;
    if (empty($userId)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID de usuario no proporcionado',
            'errors' => ['user_id' => 'El ID de usuario es requerido']
        ]);
        exit;
    }

    // Validación de seguridad para administradores
    $users = new Users();
    $currentUserRole = $datosUsuario['role_id'];
    $editedUserRole = $users->get_user_role($userId);

    if ($currentUserRole == 2 && $userId == $datosUsuario['user_id'] && $_POST['role_id'] != 2) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Acción no permitida',
            'errors' => ['role_id' => 'No puedes cambiar tu propio rol como administrador']
        ]);
        exit;
    }

    // Validación de datos
    $errors = [];
    if (empty($_POST['usuario'])) $errors['usuario'] = 'Nombre de usuario requerido';
    if (empty($_POST['correo'])) $errors['correo'] = 'Correo electrónico requerido';
    if (!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) $errors['correo'] = 'Formato de correo inválido';
    if (empty($_POST['role_id'])) $errors['role_id'] = 'Rol requerido';

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $errors
        ]);
        exit;
    }

    // Procesar imagen de perfil
    $url_pic = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        try {
            $fileManager = new FileManager();
            $url_pic = $fileManager->uploadProfilePicture(
                $_FILES['profile_picture'],
                $datosUsuario['company_id'],
                $_POST['usuario'],
                $userId
            );
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error al subir la imagen',
                'errors' => ['profile_picture' => $e->getMessage()]
            ]);
            exit;
        }
    }

    // Preparar datos para actualización
    $userData = [
        'id' => $userId,
        'company_id' => $datosUsuario['company_id'],
        'username' => $_POST['usuario'],
        'email' => $_POST['correo'],
        'role_id' => $_POST['role_id'],
        'description' => $_POST['descripcion'] ?? null,
        'url_pic' => $url_pic
    ];

    // Actualizar usuario
    if (!$users->update_user($userData)) {
        throw new Exception('Error al actualizar el usuario en la base de datos');
    }

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Usuario actualizado correctamente',
        'data' => ['user_id' => $userId]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor',
        'errors' => ['server' => $e->getMessage()]
    ]);
}
