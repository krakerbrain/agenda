<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__) . '/classes/Users.php';

$auth = new JWTAuth();
$userData = $auth->validarTokenUsuario();
$company_id = $userData['company_id'] != 0 ? $userData['company_id'] : $_POST['company_id'];

if (isset($_POST['usuario']) && isset($_POST['correo']) && isset($_POST['password']) && isset($_POST['password2'])) {
    // Crear una instancia de la clase User
    $user = new Users();
    $data = [
        'role_id' => $_POST['role_id'],
        'username' => $_POST['usuario'],
        'email' => $_POST['correo'],
        'password' => $_POST['password'],
        'password2' => $_POST['password2'],
        'company_id' => $company_id
    ];

    // Llamar a la función para registrar al usuario

    if (!empty($data['username']) && !empty($data['email']) && !empty($data['password']) && !empty($data['password2'])) {
        $register_result = $user->register_user($data);

        echo json_encode($register_result);
    }
} else {
    echo json_encode(["success" => false, "error" => "Datos no válidos"]);
}
