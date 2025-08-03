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

    public function getSocialForDatosEmpresa()
    {
        $sql = "SELECT id, name FROM social_networks ORDER BY name";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getMaxOrderForCompany(): int
    {
        try {
            $sql = "SELECT COALESCE(MAX(orden), 0) 
                FROM company_social_networks 
                WHERE company_id = :company_id";

            $this->db->query($sql);
            $this->db->bind(':company_id', $this->company_id);

            // Usando singleValue() que retorna directamente el valor
            $maxOrder = $this->db->singleValue();

            return (int)$maxOrder; // Aseguramos tipo entero

        } catch (Exception $e) {
            error_log("Error al obtener el máximo orden: " . $e->getMessage());
            return 0;
        }
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

            // Obtener el siguiente orden disponible
            $nextOrder = $this->getNextOrder();

            // Inserta la nueva red social en company_social_networks
            $sql = "INSERT INTO company_social_networks 
                (company_id, social_network_id, url, orden) 
                VALUES (:company_id, :social_network_id, :url, :orden)";

            $this->db->query($sql);
            $this->db->bind(':company_id', $this->company_id);
            $this->db->bind(':social_network_id', $socialNetworkId);
            $this->db->bind(':url', $url);
            $this->db->bind(':orden', $nextOrder);
            $this->db->execute();

            return ['success' => true, 'message' => 'Red social agregada correctamente'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error al agregar la red social: ' . $e->getMessage()];
        }
    }

    private function getNextOrder(): int
    {
        $sql = "SELECT MAX(orden) as max_order FROM company_social_networks WHERE company_id = :company_id";
        $this->db->query($sql);
        $this->db->bind(':company_id', $this->company_id);
        $result = $this->db->singleValue();
        return $result ? (int)$result + 1 : 1; // Si no hay ordenes, empieza en 1
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
                WHERE csn.company_id = :company_id
                ORDER BY csn.orden ASC";

        $this->db->query($sql);
        $this->db->bind(':company_id', $this->company_id);

        return $this->db->resultSet(); // Devuelve todos los resultados como array asociativo
    }

    public function updateSocialOrder($id, $order)
    {
        try {
            // Primero validar que la red social pertenece a la compañía
            $sql = "SELECT id FROM company_social_networks 
                WHERE id = :id AND company_id = :company_id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':company_id', $this->company_id);
            $result = $this->db->single();

            if (!$result) {
                return ['success' => false, 'error' => 'Red social no encontrada o no pertenece a esta compañía'];
            }

            // Actualizar el orden
            $sql = "UPDATE company_social_networks 
                SET orden = :orden 
                WHERE id = :id AND company_id = :company_id";

            $this->db->query($sql);
            $this->db->bind(':orden', $order);
            $this->db->bind(':id', $id);
            $this->db->bind(':company_id', $this->company_id);
            $this->db->execute();

            return ['success' => true, 'message' => 'Orden actualizado correctamente'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error al actualizar el orden: ' . $e->getMessage()];
        }
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


    // public function setPreferredSocial($socialId)
    // {
    //     try {
    //         $this->db->beginTransaction();

    //         // Desmarcar la red preferida actual
    //         $sql = "UPDATE company_social_networks SET red_preferida = 0 WHERE company_id = :company_id";
    //         $this->db->query($sql);
    //         $this->db->bind(':company_id', $this->company_id);
    //         $this->db->execute();

    //         // Marcar la nueva red social como preferida
    //         $sql = "UPDATE company_social_networks SET red_preferida = 1 WHERE id = :id";
    //         $this->db->query($sql);
    //         $this->db->bind(':id', $socialId);
    //         $this->db->execute();

    //         $this->db->endTransaction();
    //         return ['success' => true, 'message' => 'Red social preferida actualizada correctamente'];
    //     } catch (Exception $e) {
    //         $this->db->cancelTransaction();
    //         return ['success' => false, 'error' => 'Error al actualizar la red social preferida: ' . $e->getMessage()];
    //     }
    // }
}
