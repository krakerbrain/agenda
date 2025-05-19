<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__, 2) . '/classes/EmailSender.php';

try {
    // Validar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // Validar campos requeridos
    $requiredFields = ['nombre', 'email', 'mensaje'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception('Faltan campos requeridos', 400);
        }
    }

    // Sanitizar y validar datos
    $nombre = trim($_POST['nombre']);
    $nombre = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $mensaje = trim($_POST['mensaje']);
    $mensaje = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email no válido', 400);
    }

    // Procesar el email
    $emailTemplate = new EmailTemplate();
    $mailContent = $emailTemplate->buildContactEmail($nombre, $email, $mensaje);

    $emailSender = new EmailSender();
    $emailSender->sendContactEmail($mailContent);

    // Al final, devolver respuesta JSON exitosa:
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => 'Mensaje enviado con éxito'
    ]);
    exit;
} catch (Exception $e) {
    error_log("Error en procesar_contacto: " . $e->getMessage());
    $errorCode = $e->getCode() ?: 500;
    $errorMessage = 'Ocurrió un error al procesar tu mensaje';

    if ($errorCode === 400) {
        $errorMessage = 'Datos del formulario no válidos';
    }

    // Respuesta JSON de error
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($errorCode);
    echo json_encode([
        'success' => false,
        'message' => $errorMessage
    ]);
    exit;
}
