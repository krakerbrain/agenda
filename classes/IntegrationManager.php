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

    public function createDefaultIntegrationsForCompany($companyId)
    {
        try {
            // Obtener todas las integraciones disponibles
            $this->db->query("SELECT id, name FROM integrations");
            $integrations = $this->db->resultSet();

            // Insertar cada integración con los valores predeterminados
            foreach ($integrations as $integration) {
                $defaultEnabled = ($integration['name'] === 'WhatsApp') ? 1 : 0; // WhatsApp en 12 y el resto en 0

                $this->db->query("INSERT INTO company_integrations 
                                  (company_id, integration_id, enabled) 
                                  VALUES (:company_id, :integration_id, :enabled)");
                $this->db->bind(':company_id', $companyId);
                $this->db->bind(':integration_id', $integration['id']);
                $this->db->bind(':enabled', $defaultEnabled);
                $this->db->execute();
            }

            return ['success' => true];
        } catch (Exception $e) {
            $this->logger->logError('Error al crear integraciones para la compañía con ID ' . $companyId . ': ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    /**
     * Limpia los datos de integración de Google Calendar para una compañía específica.
     *
     * Este método se utiliza para manejar casos en los que la integración con Google Calendar
     * ya no es válida, por ejemplo, debido a un token inválido (`invalid_grant`) o cualquier
     * error crítico relacionado con los tokens almacenados. 
     *
     * Al establecer el campo `integration_data` como NULL en la tabla `company_integrations`, 
     * se fuerza al sistema a requerir una nueva autenticación por parte del usuario para
     * restablecer la integración.
     *
     * @param int $companyId El ID de la compañía para la cual se eliminarán los datos de integración.
     * 
     * Proceso:
     * - Actualiza la columna `integration_data` en la tabla `company_integrations` a NULL.
     * - Utiliza un parámetro enlazado para proteger contra inyecciones SQL.
     * 
     * Escenarios de Uso:
     * - Cuando se detecta un error `invalid_grant` al intentar renovar el token.
     * - Cuando la integración con Google Calendar ya no es válida o es inconsistente.
     *
     * Nota:
     * Este método no genera una nueva autenticación automáticamente, 
     * pero prepara el sistema para que requiera la autenticación nuevamente.
     */
    public function clearGoogleCalendarIntegration($companyId)
    {
        $this->db->query("UPDATE company_integrations SET integration_data = NULL WHERE company_id = :companyId");

        $this->db->bind(':companyId', $companyId);
        $this->db->execute();
    }

    public function handleWhatsAppIntegration($company_id, $enable)
    {
        try {
            // WhatsApp tiene integration_id = 1
            $this->db->query("UPDATE company_integrations 
                SET enabled = :enabled 
                WHERE company_id = :company_id AND integration_id = 2");

            $this->db->bind(':enabled', $enable ? 1 : 0);
            $this->db->bind(':company_id', $company_id);

            return $this->db->execute();
        } catch (Exception $e) {
            $this->logger->logError('Error al modificar estado de WhatsApp: ' . $e->getMessage());
            return false;
        }
    }
}
