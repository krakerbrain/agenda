<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/Database.php';
class ActivationTokenService
{
    private $db;
    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? new Database();
    }

    /**
     * Crea un token de activación para un usuario
     */
    public function createTokenForUser(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 day'));

        try {
            $this->db->query("INSERT INTO user_activation_tokens (user_id, token, expires_at) 
                              VALUES (:user_id, :token, :expires)");
            $this->db->bind(':user_id', $userId);
            $this->db->bind(':token', $token);
            $this->db->bind(':expires', $expires);
            $this->db->execute();

            return $token;
        } catch (Exception $e) {
            error_log("Error al generar token de activación: " . $e->getMessage());
            throw new Exception("No se pudo generar el token de activación");
        }
    }

    /**
     * Valida si el token es válido y no ha expirado
     */
    public function validateToken(string $token): ?int
    {
        try {
            $this->db->query("SELECT user_id FROM user_activation_tokens 
                              WHERE token = :token AND expires_at > NOW()");
            $this->db->bind(':token', $token);
            $result = $this->db->single();

            return $result ? $result['user_id'] : null;
        } catch (Exception $e) {
            error_log("Error al validar token de activación: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Elimina el token después de activarlo o invalidarlo
     */
    public function deleteToken(string $token): bool
    {
        try {
            $this->db->query("DELETE FROM user_activation_tokens WHERE token = :token");
            $this->db->bind(':token', $token);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error al eliminar token de activación: " . $e->getMessage());
            return false;
        }
    }

    public function hasActiveToken($userId)
    {
        try {
            $this->db->query("SELECT COUNT(*) as count FROM user_activation_tokens 
                              WHERE user_id = :user_id AND expires_at > NOW()");
            $this->db->bind(':user_id', $userId);
            $result = $this->db->single();

            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Error al verificar token de activación: " . $e->getMessage());
            return false;
        }
    }
}
