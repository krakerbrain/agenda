<?php

require_once 'DatabaseSessionManager.php'; // Asegúrate de tener la ruta correcta
require_once 'ConfigUrl.php'; // Asegúrate de tener la ruta correcta

class EmailTemplate
{
    private $conn;
    private $baseUrl;
    private $companyData = null;
    private $serviceData = null;
    private $userData = null;


    public function __construct()
    {
        $manager = new DatabaseSessionManager();
        $baseUrl = new ConfigUrl();
        $this->conn = $manager->getDB();
        $this->baseUrl = $baseUrl->get();
    }

    // Obtener plantillas por company_id
    public function getTemplatesByCompanyId($company_id)
    {
        try {
            $query = $this->conn->prepare("SELECT notas_correo_reserva as reserva, notas_correo_confirmacion as confirmacion FROM companies WHERE id = :company_id");
            $query->bindParam(':company_id', $company_id, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Actualizar una plantilla existente
    public function updateTemplate($company_id, $template_name, $notas)
    {
        try {
            $query = $this->conn->prepare("
            UPDATE companies
            SET notas_correo_" . $template_name . " = :notas
            WHERE id = :company_id");

            // Almacenar el JSON en una variable antes de pasarlo a bindParam
            $notasJson = json_encode($notas);
            $query->bindParam(':company_id', $company_id, PDO::PARAM_INT);
            $query->bindParam(':notas', $notasJson, PDO::PARAM_STR);
            $query->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    // Validar datos de la plantilla
    public static function validateTemplateData($data)
    {
        $errors = [];
        if (empty($data['company_id']) || !is_numeric($data['company_id'])) {
            $errors[] = 'ID de compañía inválido.';
        }
        if (empty($data['template_name'])) {
            $errors[] = 'Nombre de plantilla es requerido.';
        }
        return $errors;
    }

    // Cargar datos de la compañía
    private function loadCompanyData($company_id, $templateType)
    {
        if ($this->companyData === null) {
            $query = $this->conn->prepare("SELECT name, logo, notas_correo_" . $templateType . " as notas, social_token FROM companies WHERE id = :company_id LIMIT 1");
            $query->bindParam(':company_id', $company_id);
            $query->execute();
            $this->companyData = $query->fetch(PDO::FETCH_ASSOC);

            if (!$this->companyData) {
                throw new Exception("Empresa no encontrada.");
            }

            $this->companyData['logo'] = 'https://agenda2024.online/' . $this->companyData['logo'];
            $this->companyData['notas'] = json_decode($this->companyData['notas'], true);
        }
    }

    // Cargar datos del servicio
    private function loadServiceData($service_id)
    {
        if ($this->serviceData === null) {
            $serviceQuery = $this->conn->prepare("SELECT name FROM services WHERE id = :service_id LIMIT 1");
            $serviceQuery->bindParam(':service_id', $service_id);
            $serviceQuery->execute();
            $this->serviceData = $serviceQuery->fetch(PDO::FETCH_ASSOC);

            if (!$this->serviceData) {
                throw new Exception("Servicio no encontrado.");
            }
        }
    }

    private function loadUserData($company_id)
    {
        if ($this->userData === null) {
            $userQuery = $this->conn->prepare("SELECT name, email FROM users WHERE company_id = :company_id LIMIT 1");
            $userQuery->bindParam(':company_id', $company_id);
            $userQuery->execute();
            $this->userData = $userQuery->fetch(PDO::FETCH_ASSOC);

            if (!$this->userData) {
                throw new Exception("Usuario no encontrado.");
            }
        }
    }

    // Construir el correo
    public function buildEmail($company_id, $templateType, $service_id, $name, $date, $startTime)
    {
        // Cargar datos de la compañía y del servicio
        $this->loadCompanyData($company_id, $templateType);
        $this->loadServiceData($service_id);

        $notesHtml = '';
        if (!empty($this->companyData['notas'])) {
            foreach ($this->companyData['notas'] as $note) {
                $notesHtml .= "<li>{$note}</li>";
            }
        } else {
            $notesHtml = '<li>No hay notas adicionales.</li>';
        }

        $templatePath = $this->baseUrl . 'correos_template/correo_' . $templateType . '.php';
        $templateContent = file_get_contents($templatePath);

        $date = date('d/m/Y', strtotime($date));
        $startTime = date('h:i a', strtotime($startTime));

        $subject_msg = $templateType == 'reserva' ? 'Solicitud de reserva recibida - {fecha_reserva}' : '¡Tu reserva ha sido confirmada! - {fecha_reserva}';
        $subject_msg = str_replace('{fecha_reserva}', $date, $subject_msg);
        $subject = mb_encode_mimeheader($subject_msg, 'UTF-8', 'B', "\n");

        $body = str_replace(
            ['{nombre_cliente}', '{fecha_reserva}', '{hora_reserva}', '{servicio_reservado}', '{notas}', '{ruta_logo}', '{nombre_empresa}'],
            [$name, $date, $startTime, $this->serviceData['name'], $notesHtml, $this->companyData['logo'], $this->companyData['name']],
            $templateContent
        );

        return ['subject' => $subject, 'body' => $body, 'company_name' => $this->companyData['name'], 'social_token' => $this->companyData['social_token']];
    }

    // Otro constructor de correos, como alertas, reutilizando los mismos datos
    public function buildAppointmentAlert($company_id, $name, $date, $startTime)
    {
        if ($this->companyData === null || $this->serviceData === null) {
            throw new Exception("Los datos de la compañía y el servicio deben cargarse primero.");
        }

        $this->loadUserData($company_id);

        $alertTemplatePath = $this->baseUrl . 'correos_template/correo_aviso_reserva.php';
        $alertContent = file_get_contents($alertTemplatePath);

        $date = date('d/m/Y', strtotime($date));
        $startTime = date('h:i a', strtotime($startTime));

        $body = str_replace(
            ['{ruta_logo}', '{nombre_usuario}', '{nombre_cliente}', '{fecha}', '{hora}', '{nombre_servicio}'],
            [$this->companyData['logo'], $this->userData['name'], $name, $date, $startTime, $this->serviceData['name']],
            $alertContent
        );

        return ['subject' => 'Alerta de cita', 'body' => $body, 'correo_empresa' => $this->userData['email']];
    }
}
