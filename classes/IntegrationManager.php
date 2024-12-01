<?php

require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/Database.php';
require_once dirname(__DIR__) . '/classes/Logger.php';

class IntegrationManager
{
    private $db;
    private $logger;

    public function __construct()
    {
        $this->db = new Database(); // Usa la clase Database
        $this->logger = new Logger(); // Usa la clase Logger
    }

    public function getCompanyIntegrations($companyId)
    {
        $this->db->query("SELECT 
                              i.id AS integration_id, 
                              i.name, 
                              i.enabled AS global_enabled,  -- Estado global de la integración
                              COALESCE(ci.enabled, 0) AS company_enabled -- Estado a nivel de compañía
                          FROM integrations AS i
                          LEFT JOIN company_integrations AS ci
                          ON i.id = ci.integration_id AND ci.company_id = :company_id
                          WHERE i.enabled = 1"); // Solo muestra integraciones habilitadas globalmente
        $this->db->bind(':company_id', $companyId);
        return $this->db->resultSet();
    }

    // Método para verificar si la integración es de Google Calendar
    public function isGoogleCalendarIntegration($integrationId)
    {
        $this->db->query("SELECT name FROM integrations WHERE id = :integration_id");
        $this->db->bind(':integration_id', $integrationId);
        $row = $this->db->single();

        return ($row && $row['name'] === 'Google Calendar');
    }

    // Método para obtener la integración de Google Calendar de una compañía
    public function getGoogleCalendarIntegration($companyId)
    {
        $this->db->query("SELECT * FROM company_integrations WHERE company_id = :company_id");
        $this->db->bind(':company_id', $companyId);
        $integration = $this->db->single();

        // Verificar si la integración es de Google Calendar
        if ($integration && $this->isGoogleCalendarIntegration($integration['integration_id'])) {
            return [
                'integration_data' => json_decode($integration['integration_data'], true),
                'enabled' => (bool) $integration['enabled'] // Devolver el estado de la integración como booleano
            ];
        }

        // Si no existe integración o no es Google Calendar, devolver null
        return null;
    }

    // Método para guardar datos de integración de Google Calendar
    public function saveGoogleCalendarIntegration($companyId, $data)
    {
        $encodedData = json_encode($data);

        $this->db->query("UPDATE company_integrations SET integration_data = :data, enabled = 1, updated_at = NOW() WHERE company_id = :company_id AND integration_id IN (SELECT id FROM integrations WHERE name = 'Google Calendar')");
        $this->db->bind(':data', $encodedData);
        $this->db->bind(':company_id', $companyId);

        // Ejecutar la actualización
        return $this->db->execute();
    }
    // Método para guardar datos de integración de Google Calendar
    public function enableGoogleCalendarIntegration($companyId)
    {


        $this->db->query("UPDATE company_integrations SET enabled = 1, updated_at = NOW() WHERE company_id = :company_id AND integration_id IN (SELECT id FROM integrations WHERE name = 'Google Calendar')");

        $this->db->bind(':company_id', $companyId);

        // Ejecutar la actualización
        return $this->db->execute();
    }


    // En tu clase IntegrationManager
    public function disableGoogleCalendarIntegration($companyId)
    {
        // Actualizar el estado en la base de datos
        $this->db->query("UPDATE company_integrations SET integration_data = NULL, enabled = 0 WHERE company_id = :company_id AND integration_id IN (SELECT id FROM integrations WHERE name = 'Google Calendar')");
        $this->db->bind(':company_id', $companyId);
        // Ejecutar la actualización
        return $this->db->execute();
    }
}
