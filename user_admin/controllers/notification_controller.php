<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';

header('Content-Type: application/json');

$auth = new JWTAuth();
$userData = $auth->validarTokenUsuario();
$notifications = new Notifications();

$response = ['success' => false, 'message' => 'Acción no válida'];

try {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'create':
            if (!in_array($userData['role_id'], [1, 2])) {
                $response = ['success' => false, 'message' => 'No autorizado'];
                break;
            }

            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['title']) || empty($data['description'])) {
                $response = ['success' => false, 'message' => 'Título y descripción son requeridos'];
                break;
            }

            // Crear notificación
            $notificationId = $notifications->createNotification(
                $data['title'],
                $data['description'],
                $data['version'] ?? "1.0.0",
                $data['type'],
            );

            if ($notificationId) {
                // Distribuir a usuarios
                $notifications->distributeNotifications($notificationId['notification_id']);

                // Opcional: enviar por correo
                // if ($data['send_email'] ?? false) {
                //     $notifications->sendEmailNotification($notificationId);
                // }

                $response = ['success' => true, 'message' => 'Notificación creada'];
            } else {
                $response = ['success' => false, 'message' => 'Error al crear notificación'];
            }
            break;
        case 'getUnread':
            $unread = $notifications->getUnreadNotifications($userData['user_id']);
            $response = ['success' => true, 'notifications' => $unread];
            break;

        case 'getUnreadCount':
            $count = $notifications->getUnreadCount($userData['user_id']);
            $response = ['success' => true, 'count' => $count];
            break;

        case 'markAsRead':
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['notification_id'])) {
                $success = $notifications->markAsRead($data['notification_id'], $userData['user_id']);
                $response = ['success' => $success, 'message' => $success ? 'Marcado como leído' : 'Error al actualizar'];
            }
            break;

        case 'getAll':
            $all = $notifications->getAllNotifications($userData['user_id']);
            $response = ['success' => true, 'notifications' => $all];
            break;
        case 'markAllAsRead':
            $success = $notifications->markAllAsRead($userData['user_id']);
            $response = [
                'success' => $success,
                'message' => $success ? 'Todas las notificaciones marcadas como leídas' : 'Error al actualizar'
            ];
            break;
        default:
            $response['message'] = 'Acción no reconocida';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
