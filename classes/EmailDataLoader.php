<?php
class EmailDataLoader
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getCompanyData($company_id, $templateType = null)
    {
        // Columnas base que siempre se solicitan
        $columns = ['name', 'logo', 'social_token'];

        // Agregar columna de notas solo si se especifica templateType
        if (!empty($templateType)) {
            $columns[] = "notas_correo_{$templateType} as notas";
        }

        $this->db->query("SELECT " . implode(', ', $columns) . " FROM companies WHERE id = :company_id");
        $this->db->bind(':company_id', $company_id);
        $data = $this->db->single();

        if (!$data) {
            throw new Exception("Empresa no encontrada.");
        }

        // Procesamiento común
        $data['logo'] = 'https://agendarium.com/' . $data['logo'];

        // Procesar notas solo si existen en los datos
        $data['notas'] = isset($data['notas']) ? json_decode($data['notas'], true) : [];

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
