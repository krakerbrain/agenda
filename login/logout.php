<?php
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
session_start();
session_destroy();
// Limpiar las variables de sesión
$_SESSION = array();  // Alternativamente puedes usar: session_unset();

// Eliminar la cookie PHPSESSID (opcional, pero recomendable)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
// Remove JWT from cookies
setcookie('jwt', '', time() - 3600, '/');
setcookie('superadmin_jwt', '', time() - 3600, '/');

header("Location: $baseUrl"); // Redirect to baseUrl
exit();
