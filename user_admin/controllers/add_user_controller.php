<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/UserRegistrationService.php';
require_once dirname(__DIR__, 2) . '/classes/FileManager.php';

header('Content-Type: application/json');

// Validar método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'title' => 'Método no permitido', // Nuevo campo para el título
        'message' => 'Solo se permiten solicitudes POST' // Mensaje para el modal
    ]);
    exit;
}

try {
    $auth = new JWTAuth();
    $userData = $auth->validarTokenUsuario();

    // Validar permisos
    if ($userData['role_id'] > 2) {
        throw new Exception('No tienes permisos para esta acción');
    }

    // Obtener company_id
    $company_id = $userData['company_id'] ?? $_POST['company_id'] ?? null;
    if (empty($company_id)) {
        throw new Exception('ID de compañía no proporcionado');
    }

    // Inicializar url_pic
    $url_pic = null;

    // Validar datos requeridos
    $errors = [];
    $requiredFields = [
        'usuario' => 'Nombre de usuario requerido',
        'correo' => 'Correo electrónico requerido',
        'password' => 'Contraseña requerida',
        'password2' => 'Confirmación de contraseña requerida',
        'role_id' => 'Rol requerido'
    ];

    foreach ($requiredFields as $field => $message) {
        if (empty($_POST[$field])) {
            $errors[$field] = $message;
        }
    }

    // Validación de email
    if (!empty($_POST['correo']) && !filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
        $errors['correo'] = 'Correo electrónico inválido';
    }

    // Validar coincidencia de contraseñas
    if (!empty($_POST['password']) && $_POST['password'] !== $_POST['password2']) {
        $errors['password2'] = 'Las contraseñas no coinciden';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'title' => 'Validación fallida',
            'message' => 'Por favor corrige los siguientes errores',
            'errors' => $errors
        ]);
        exit;
    }

    // Procesar imagen si existe
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        try {
            $url_pic = uploadImage($_FILES['profile_picture'], $company_id, $_POST['usuario']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'title' => 'Error al subir imagen',
                'message' => $e->getMessage(),
                'errors' => ['profile_picture' => $e->getMessage()]
            ]);
            exit;
        }
    }

    // Registrar usuario
    $userRegistration = new UserRegistrationService();
    $result = $userRegistration->registerUser([
        'username' => trim($_POST['usuario']),
        'email' => trim($_POST['correo']),
        'password' => $_POST['password'],
        'password2' => $_POST['password2'],
        'role_id' => (int)$_POST['role_id'],
        'company_id' => $company_id,
        'url_pic' => $url_pic,
        'description' => trim($_POST['descripcion'] ?? '')
    ], $company_id, false);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'title' => 'Éxito', // Título para el modal
            'message' => 'Usuario registrado exitosamente',
            'user_id' => $result['user_id'] ?? null,
            'url_pic' => $url_pic
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'title' => 'Error al registrar usuario',
            'message' => $result['error'] ?? 'Error desconocido al registrar usuario',
            'errors' => $result['errors'] ?? []
        ]);
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'title' => 'Error en el servidor',
        'message' => $e->getMessage()
    ]));
}

function uploadImage($file, $company_id, $user_name = '', $user_id = null)
{
    try {
        $fileManager = new FileManager();
        return $fileManager->uploadProfilePicture($file, $company_id, $user_name, $user_id);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'title' => 'Error al subir imagen',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}
