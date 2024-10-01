<?php

require_once dirname(__DIR__) . '/classes/Database.php';

class RedesSociales
{
    private $db;
    private $company_id;

    public function __construct($company_id)
    {
        $this->db = new Database(); // Usa la clase Database
        $this->company_id = $company_id;
    }

    public function agregarRedSocial($socialNetworkId, $url)
    {
        try {
            $verification = $this->verifySocialNetwork();
            // Verifica si hay redes sociales existentes
            if ($verification == 0) {
                // Si no hay redes sociales, se genera un token para la columna social_token de company
                $token = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
                $sql = "UPDATE companies SET social_token = :token WHERE id = :company_id";
                $this->db->query($sql);
                $this->db->bind(':token', $token);
                $this->db->bind(':company_id', $this->company_id);
                $this->db->execute();
            }
            $preferred = $verification  == 0 ? 1 : 0;

            // Inserta la nueva red social en company_social_networks
            $sql = "INSERT INTO company_social_networks (company_id, social_network_id, url, red_preferida) VALUES (:company_id, :social_network_id, :url, :preferred)";
            $this->db->query($sql);
            $this->db->bind(':company_id', $this->company_id);
            $this->db->bind(':social_network_id', $socialNetworkId);
            $this->db->bind(':url', $url);
            $this->db->bind(':preferred', $preferred);
            $this->db->execute();

            return ['success' => true, 'message' => 'Red social agregada correctamente'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error al agregar la red social: ' . $e->getMessage()];
        }
    }

    public function verifySocialNetwork()
    {
        // Cuenta cuántas redes sociales existen para la compañía
        $sql = "SELECT COUNT(*) as total FROM company_social_networks WHERE company_id = :company_id";
        $this->db->query($sql);
        $this->db->bind(':company_id', $this->company_id);
        // Retorna el conteo de redes sociales
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }


    public function obtenerRedesSociales()
    {
        $sql = "SELECT csn.*, sn.name as nombre FROM company_social_networks csn 
                JOIN social_networks sn ON csn.social_network_id = sn.id 
                WHERE csn.company_id = :company_id";

        $this->db->query($sql);
        $this->db->bind(':company_id', $this->company_id);

        return $this->db->resultSet(); // Devuelve todos los resultados como array asociativo
    }


    public function deleteSocial($socialId)
    {
        try {
            $this->db->beginTransaction();

            if ($this->verifySocialNetwork() == 1) {
                $sql = "UPDATE companies SET social_token = NULL WHERE id = :company_id";
                $this->db->query($sql);
                $this->db->bind(':company_id', $this->company_id);
                $this->db->execute();
            }

            $sql = "DELETE FROM company_social_networks WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $socialId);
            $this->db->execute();

            $this->db->endTransaction();
            return ['success' => true, 'message' => 'Red social eliminada correctamente'];
        } catch (Exception $e) {
            $this->db->cancelTransaction();
            return ['success' => false, 'error' => 'Error al eliminar la red social: ' . $e->getMessage()];
        }
    }


    public function setPreferredSocial($socialId)
    {
        try {
            $this->db->beginTransaction();

            // Desmarcar la red preferida actual
            $sql = "UPDATE company_social_networks SET red_preferida = 0 WHERE company_id = :company_id";
            $this->db->query($sql);
            $this->db->bind(':company_id', $this->company_id);
            $this->db->execute();

            // Marcar la nueva red social como preferida
            $sql = "UPDATE company_social_networks SET red_preferida = 1 WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $socialId);
            $this->db->execute();

            $this->db->endTransaction();
            return ['success' => true, 'message' => 'Red social preferida actualizada correctamente'];
        } catch (Exception $e) {
            $this->db->cancelTransaction();
            return ['success' => false, 'error' => 'Error al actualizar la red social preferida: ' . $e->getMessage()];
        }
    }
}
