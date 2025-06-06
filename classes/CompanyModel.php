<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class CompanyModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database(); // Usa la clase Database
    }

    public function getCompanyByUrl($url)
    {
        $this->db->query("SELECT id, logo, selected_banner, name, address, phone,description, schedule_mode, calendar_mode, calendar_days_available, fixed_duration, fixed_start_date,font_color, btn2, bg_color, btn1, notas_correo_reserva, notas_correo_confirmacion FROM companies WHERE custom_url = :url AND is_active = 1");
        $this->db->bind(':url', $url);
        return $this->db->single();
    }

    public function getServicesByCompanyId($companyId)
    {
        $this->db->query("SELECT id, observations, name, duration FROM services WHERE company_id = :company_id AND is_enabled = 1");
        $this->db->bind(':company_id', $companyId);
        return $this->db->resultSet();
    }

    public function getServicesByCompanyAndUser($companyId, $userId)
    {
        $this->db->query("SELECT s.id, s.observations as service_description, s.name as service_name, s.duration FROM services s
        JOIN user_services us ON s.id = us.service_id
        JOIN users u ON us.user_id = u.id
        WHERE u.company_id = :company_id
        AND us.user_id = :userId
        AND us.is_active = 1");
        $this->db->bind(':company_id', $companyId);
        $this->db->bind(':userId', $userId);
        return $this->db->resultSet();
    }

    public function getServicesCategories($serviceId)
    {
        $this->db->query("SELECT id, category_name, category_description FROM service_categories WHERE service_id = :service_id");
        $this->db->bind(':service_id', $serviceId);
        return $this->db->resultSet();
    }

    public function getSocialNetworksByCompanyId($companyId)
    {
        $this->db->query("SELECT sn.name, sn.icon_class, csn.url 
                          FROM company_social_networks csn 
                          JOIN social_networks sn ON csn.social_network_id = sn.id 
                          WHERE csn.company_id = :company_id");
        $this->db->bind(':company_id', $companyId);
        return $this->db->resultSet();
    }

    public function getCompanyCalendarData($companyId)
    {
        $this->db->query("SELECT calendar_mode, fixed_start_date, calendar_days_available, fixed_duration FROM companies WHERE id = :company_id AND is_active = 1");
        $this->db->bind(':company_id', $companyId);
        return $this->db->single();
    }

    public function getCustomUrl($companyId)
    {
        $this->db->query("SELECT custom_url FROM companies WHERE id = :company_id AND is_active = 1");
        $this->db->bind(':company_id', $companyId);
        return $this->db->singleValue();
    }

    public function getServiceProvidersByCompanyIdCount($companyId)
    {
        try {
            $this->db->query("SELECT count(*) FROM users WHERE company_id = :company_id");
            $this->db->bind(':company_id', $companyId);
            return $this->db->singleValue();
        } catch (PDOException $e) {
            // Aquí puedes manejar el error, por ejemplo, registrarlo en un log
            error_log("Error al obtener los proveedores de usuario: " . $e->getMessage());
            return []; // O lanzar una excepción, dependiendo de tu manejo de errores
        }
    }
}
