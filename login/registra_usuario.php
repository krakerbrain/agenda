<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__) . '/classes/UserRegistrationService.php';

header('Content-Type: application/json');

// Validar método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido',
        'errors' => ['Solo se permiten solicitudes POST']
    ]);
    exit;
}

try {
    $auth = new JWTAuth();
    $userData = $auth->validarTokenUsuario();

    // Validar que el usuario tenga permisos para crear usuarios
    if ($userData['role_id'] > 2) { // Ajusta según tus roles
        throw new Exception('No tienes permisos para esta acción');
    }

    // Obtener company_id de forma segura
    $company_id = $userData['company_id'] ?? null;
    if (empty($company_id)) {
        $company_id = $_POST['company_id'] ?? null;
        if (empty($company_id)) {
            throw new Exception('ID de compañía no proporcionado');
        }
    }

    // Validar datos requeridos con mensajes más descriptivos
    $errors = [];
    if (empty($_POST['usuario'])) $errors['usuario'] = 'Nombre de usuario requerido';
    if (empty($_POST['correo'])) $errors['correo'] = 'Correo electrónico requerido';
    if (!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) $errors['correo'] = 'Correo electrónico inválido';
    if (empty($_POST['password'])) $errors['password'] = 'Contraseña requerida';
    if (empty($_POST['password2'])) $errors['password2'] = 'Confirmación de contraseña requerida';
    if ($_POST['password'] !== $_POST['password2']) $errors['password2'] = 'Las contraseñas no coinciden';
    if (empty($_POST['role_id'])) $errors['role_id'] = 'Rol requerido';

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Validación fallida',
            'errors' => $errors
        ]);
        exit;
    }

    // Registrar usuario
    $userRegistration = new UserRegistrationService();
    $result = $userRegistration->registerUser([
        'username' => trim($_POST['usuario']),
        'email' => trim($_POST['correo']),
        'password' => $_POST['password'],
        'password2' => $_POST['password2'],
        'role_id' => (int)$_POST['role_id'],
        'company_id' => $company_id
    ], $company_id, true);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'user_id' => $result['user_id'] ?? null
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $result['error'] ?? 'Error al registrar usuario',
            'errors' => $result['errors'] ?? []
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error en el servidor',
        'message' => $e->getMessage()
    ]);
}
