<?php
class EmailDataLoader
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getCompanyData($company_id, $templateType)
    {
        $this->db->query("SELECT name, logo, notas_correo_{$templateType} as notas, social_token FROM companies WHERE id = :company_id");
        $this->db->bind(':company_id', $company_id);
        $data = $this->db->single();

        if (!$data) {
            throw new Exception("Empresa no encontrada.");
        }

        $data['logo'] = 'https://agendarium.com/' . $data['logo'];
        $data['notas'] = json_decode($data['notas'], true);

        return $data;
    }

    public function getServiceData($service_id)
    {
        $this->db->query("SELECT name FROM services WHERE id = :service_id");
        $this->db->bind(':service_id', $service_id);
        $data = $this->db->single();

        if (!$data) {
            throw new Exception("Servicio no encontrado.");
        }

        return $data;
    }

    public function getUserData($company_id)
    {
        $this->db->query("SELECT name, email FROM users WHERE company_id = :company_id");
        $this->db->bind(':company_id', $company_id);
        $data = $this->db->single();

        if (!$data) {
            throw new Exception("Usuario no encontrado.");
        }

        return $data;
    }
}
