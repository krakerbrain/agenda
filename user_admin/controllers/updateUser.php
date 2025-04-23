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
        throw new Exception('Método no permitido');
    }

    $userId = $_POST['user_id'] ?? null;
    if (empty($userId)) {
        throw new Exception('ID de usuario no proporcionado');
    }

    // Validar datos (similar a addUser.php pero sin validar password)
    $errors = [];
    if (empty($_POST['usuario'])) $errors['usuario'] = 'Nombre requerido';
    if (empty($_POST['correo'])) $errors['correo'] = 'Correo requerido';
    if (!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) $errors['correo'] = 'Correo inválido';
    if (empty($_POST['role_id'])) $errors['role_id'] = 'Rol requerido';

    if (!empty($errors)) {
        throw new Exception('Validación fallida: ' . implode(', ', $errors));
    }

    // Procesar foto si se subió
    $url_pic = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileManager = new FileManager();
        $url_pic = $fileManager->uploadProfilePicture($_FILES['profile_picture'], $datosUsuario['company_id'], $_POST['usuario'], $userId);
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

    // Actualizar usando el método de la clase Users
    $users = new Users();
    $users->update_user($userData);


    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}