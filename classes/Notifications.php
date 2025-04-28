<?php
require_once 'Database.php';
class Notifications
{
    private $db;

    public function __construct(Database $db = null)
    {
        $this->db = $db ?: new Database();
    }

    // Para admin master: crear notificación global
    public function createGlobalNotification($title, $description, $version, $type)
    {
        $this->db->query("INSERT INTO system_notifications 
                                   (title, description, version, notification_type, is_global) 
                                      VALUES (:title, :description, :version, :type, TRUE)");
        $this->db->bind(':title', $title);
        $this->db->bind(':description', $description);
        $this->db->bind(':version', $version);
        $this->db->bind(':type', $type);
        return $this->db->execute();
    }

    // Para admin de empresa: crear notificación para su empresa
    public function createCompanyNotification($title, $description, $version, $type)
    {
        $this->db->query("INSERT INTO system_notifications 
                                   (title, description, version, notification_type) 
                                        VALUES (:title, :description, :version, :type");
        $this->db->bind(':title', $title);
        $this->db->bind(':description', $description);
        $this->db->bind(':version', $version);
        $this->db->bind(':type', $type);
        return $this->db->execute();
    }

    // Distribuir notificaciones a los usuarios
    public function distributeNotifications($notification_id)
    {
        $this->db->query("INSERT INTO user_notifications (user_id, notification_id)
                                   SELECT u.id, :notification_id FROM users u WHERE 1=1");
        $this->db->bind(':notification_id', $notification_id);

        return $this->db->execute();
    }

    // Obtener notificaciones no leídas para un usuario
    public function getUnreadNotifications($user_id, $limit = 5)
    {
        $this->db->query("SELECT sn.*, un.id as user_notification_id
                                   FROM user_notifications un
                                   JOIN system_notifications sn ON un.notification_id = sn.id
                                   WHERE un.user_id = :user_id AND un.is_read = FALSE
                                   ORDER BY sn.created_at DESC
                                   LIMIT :limit");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':limit', $limit);
        $this->db->execute();
        return $this->db->resultSet();
    }

    // Contar notificaciones no leídas
    public function getUnreadCount($user_id)
    {
        $this->db->query("SELECT COUNT(*) as count
                                   FROM user_notifications
                                   WHERE user_id = :user_id AND is_read = FALSE");
        $this->db->bind(':user_id', $user_id);
        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result['count'] : 0;
    }

    // Marcar como leída
    public function markAsRead($user_notification_id, $user_id)
    {
        $this->db->query("UPDATE user_notifications
                                   SET is_read = TRUE, read_at = NOW()
                                   WHERE id = :user_notification_id AND user_id = :user_id");
        $this->db->bind(':user_notification_id', $user_notification_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    public function markAllAsRead($user_id)
    {
        try {
            $this->db->beginTransaction();

            // Actualizar todas las notificaciones no leídas del usuario
            $this->db->query("UPDATE user_notifications 
                                      SET is_read = TRUE, read_at = NOW() 
                                      WHERE user_id = :user_id AND is_read = FALSE");
            $this->db->bind(':user_id', $user_id);
            $this->db->execute();
            $this->db->endTransaction();
            return true;
        } catch (Exception $e) {
            $this->db->cancelTransaction();
            error_log("Error en markAllAsRead: " . $e->getMessage());
            return false;
        }
    }

    // Obtener todas las notificaciones para un usuario
    public function getAllNotifications($user_id)
    {
        $this->db->query("SELECT sn.*, un.is_read, un.read_at, un.id as user_notification_id
                                   FROM user_notifications un
                                   JOIN system_notifications sn ON un.notification_id = sn.id
                                   WHERE un.user_id = :user_id     
                                   ORDER BY sn.created_at DESC");
        $this->db->bind(':user_id', $user_id);
        $this->db->execute();
        return $this->db->resultSet();
    }

    public function createNotification($title, $description, $version, $type)
    {
        try {
            $this->db->query("INSERT INTO system_notifications 
        (title, description, version, notification_type, is_global) 
        VALUES (:title, :description, :version, :type, :is_global)");

            $this->db->bind(':title', $title);
            $this->db->bind(':description', $description);
            $this->db->bind(':version', $version);
            $this->db->bind(':type', $type);
            $this->db->bind(':is_global', 1);

            if ($this->db->execute()) {
                return [
                    'success' => true,
                    'notification_id' => $this->db->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Error al ejecutar la inserción'
                ];
            }
        } catch (Exception $e) {

            return [
                'success' => false,
                'error' => 'Error al crear la notificación: ' . $e->getMessage(),
            ];
        }
    }
}
