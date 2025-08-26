<?php

require_once dirname(__DIR__) . '/classes/Database.php';
class FeatureManager
{
    private $db;

    public function __construct()
    {
        $this->db = new Database(); // Usa la clase Database
    }

    /**
     * Trae todos los feature flags junto con el nombre de la compañía.
     */
    public function getAllFlags()
    {
        try {
            $this->db->query('
                SELECT f.id, f.feature_name, f.enabled, c.name as company_name
                FROM feature_flags f
                JOIN companies c ON f.company_id = c.id
                ORDER BY f.id DESC
            ');
            return $this->db->resultSet();
        } catch (Exception $e) {
            throw new Exception("Error al obtener feature flags: " . $e->getMessage());
        }
    }

    /**
     * Trae todos los flags para una compañía específica.
     */
    public function getFlagsByCompany(int $companyId): array
    {
        $this->db->query('
        SELECT feature_name, enabled
        FROM feature_flags
        WHERE company_id = :company_id
    ');
        $this->db->bind(':company_id', $companyId, PDO::PARAM_INT);
        $rows = $this->db->resultSet();
        $flags = [];
        foreach ($rows as $row) {
            $flags[$row['feature_name']] = (bool)$row['enabled'];
        }
        return $flags;
    }

    /**
     * Actualiza el estado de un flag por su ID.
     */
    public function setFeatureById(int $id, bool $enabled)
    {
        try {
            $this->db->query('UPDATE feature_flags SET enabled = :enabled, updated_at = NOW() WHERE id = :id');
            $this->db->bind(':enabled', $enabled, PDO::PARAM_INT);
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            $this->db->execute();
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al actualizar el feature flag: " . $e->getMessage());
        }
    }

    /**
     * Crear un nuevo flag para una compañía (opcional)
     */
    public function createFeatureFlag(int $companyId, string $featureName, bool $enabled = false)
    {
        try {
            $this->db->query('
                INSERT INTO feature_flags (company_id, feature_name, enabled)
                VALUES (:company_id, :feature_name, :enabled)
            ');
            $this->db->bind(':company_id', $companyId, PDO::PARAM_INT);
            $this->db->bind(':feature_name', $featureName);
            $this->db->bind(':enabled', $enabled, PDO::PARAM_INT);
            $this->db->execute();
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Error al crear feature flag: " . $e->getMessage());
        }
    }

    /**
     * Chequear si un flag está habilitado para una compañía
     */
    public function isEnabled(int $companyId, string $featureName): bool
    {
        try {
            $this->db->query('
                SELECT enabled
                FROM feature_flags
                WHERE company_id = :company_id AND feature_name = :feature_name
                LIMIT 1
            ');
            $this->db->bind(':company_id', $companyId, PDO::PARAM_INT);
            $this->db->bind(':feature_name', $featureName);
            $row = $this->db->single();
            return $row && $row['enabled'] == 1;
        } catch (Exception $e) {
            throw new Exception("Error al verificar feature flag: " . $e->getMessage());
        }
    }
}
