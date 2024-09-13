<?php
class RedesSociales
{
    private $conn;
    private $company_id;

    public function __construct($conn, $company_id)
    {
        $this->conn = $conn;
        $this->company_id = $company_id;
    }

    public function agregarRedSocial($socialNetworkId, $url)
    {
        try {
            $this->conn->beginTransaction();
            //verifica resultado de la funcion verifySocialNetwork
            if ($this->verifySocialNetwork() == 0) {
                //si no hay redes sociales, se crea un token para la columna social_token de company. tiene que ser un token corto pero con letras y numeros
                $token = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
                $sql = $this->conn->prepare("UPDATE companies SET social_token = :token WHERE id = :company_id");
                $sql->bindParam(':token', $token);
                $sql->bindParam(':company_id', $this->company_id);
                $sql->execute();
            }

            $sql = $this->conn->prepare("INSERT INTO company_social_networks (company_id, social_network_id, url) VALUES (:company_id, :social_network_id, :url)");
            $sql->bindParam(':company_id', $this->company_id);
            $sql->bindParam(':social_network_id', $socialNetworkId);
            $sql->bindParam(':url', $url);
            $sql->execute();
            $this->conn->commit();
            return ['success' => true, 'message' => 'Red social agregada correctamente'];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'error' => 'Error al agregar la red social: ' . $e->getMessage()];
        }
    }

    public function verifySocialNetwork()
    {
        //esta funcion cuenta si hay una red social y devuelve un booleano
        $sql = $this->conn->prepare("SELECT COUNT(*) as total FROM company_social_networks WHERE company_id = :company_id");
        $sql->bindParam(':company_id', $this->company_id);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        //retorna el conteo de redes sociales
        return $result['total'];
    }

    public function obtenerRedesSociales()
    {
        $media = $this->conn->prepare("SELECT csn.*, sn.name as nombre FROM company_social_networks csn JOIN social_networks sn ON csn.social_network_id = sn.id WHERE csn.company_id = :company_id");
        $media->bindParam(':company_id', $this->company_id);
        $media->execute();
        return $media->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteSocial($socialId)
    {
        try {
            $this->conn->beginTransaction();
            //verifica resultado de la funcion verifySocialNetwork
            if ($this->verifySocialNetwork() == 1) {
                //si no hay redes sociales, se cambia a null el token de la columna social_token de company
                $sql = $this->conn->prepare("UPDATE companies SET social_token = NULL WHERE id = :company_id");
                $sql->bindParam(':company_id', $this->company_id);
                $sql->execute();
            }
            $sql = $this->conn->prepare("DELETE FROM company_social_networks WHERE id = :id");
            $sql->bindParam(':id', $socialId);
            $sql->execute();
            $this->conn->commit();
            return ['success' => true, 'message' => 'Red social eliminada correctamente'];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'error' => 'Error al eliminar la red social: ' . $e->getMessage()];
        }
    }

    public function setPreferredSocial($socialId)
    {
        try {
            $this->conn->beginTransaction();
            $sql = $this->conn->prepare("UPDATE company_social_networks SET red_preferida = 0 WHERE company_id = :company_id");
            $sql->bindParam(':company_id', $this->company_id);
            $sql->execute();

            $sql = $this->conn->prepare("UPDATE company_social_networks SET red_preferida = 1 WHERE id = :id");
            $sql->bindParam(':id', $socialId);
            $sql->execute();
            $this->conn->commit();
            return ['success' => true, 'message' => 'Red social preferida actualizada correctamente'];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'error' => 'Error al actualizar la red social preferida: ' . $e->getMessage()];
        }
    }
}