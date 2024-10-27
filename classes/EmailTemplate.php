<?php

require_once 'Database.php';
require_once 'ConfigUrl.php';
require_once 'EmailSender.php';

class EmailTemplate
{
    private $db;
    private $baseUrl;
    private $companyData = null;
    private $serviceData = null;
    private $userData = null;


    public function __construct($db = null)
    {
        if ($db) {
            $this->db = $db;
        } else {
            $this->db = new Database();
        }

        $baseUrl = new ConfigUrl();
        $this->baseUrl = $baseUrl->get();
    }
    // Obtener plantillas por company_id
    public function getTemplatesByCompanyId($company_id)
    {
        try {
            // Preparar y ejecutar la consulta usando la instancia de Database
            $this->db->query("SELECT notas_correo_reserva as reserva, notas_correo_confirmacion as confirmacion FROM companies WHERE id = :company_id");
            $this->db->bind(':company_id', $company_id);
            return $this->db->resultSet();
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sanitize($data)
    {
        // Usar htmlspecialchars para evitar que se inyecte HTML o JavaScript
        return is_array($data) ? array_map('htmlspecialchars', $data) : htmlspecialchars($data);
    }

    // Actualizar una plantilla existente
    public function updateTemplate($company_id, $template_name, $notas)
    {
        try {
            // Sanitizar las notas
            $notas = $this->sanitize($notas);

            // Preparar la consulta
            $sql = "
        UPDATE companies
        SET notas_correo_" . $template_name . " = :notas
        WHERE id = :company_id";

            // Almacenar el JSON en una variable antes de pasarlo a bindParam
            $notasJson = json_encode($notas);

            // Preparar y ejecutar la consulta
            $this->db->query($sql);
            $this->db->bind(':company_id', $company_id);
            $this->db->bind(':notas', $notasJson);
            $this->db->execute();

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Cargar datos de la compañía
    private function loadCompanyData($company_id, $templateType)
    {
        if ($this->companyData === null) {
            $this->db->query("SELECT name, logo, notas_correo_" . $templateType . " as notas, social_token FROM companies WHERE id = :company_id LIMIT 1");
            $this->db->bind(':company_id', $company_id);
            $this->companyData = $this->db->single();

            if (!$this->companyData) {
                throw new Exception("Empresa no encontrada.");
            }

            $this->companyData['logo'] = 'https://agendarium.com/' . $this->companyData['logo'];
            $this->companyData['notas'] = json_decode($this->companyData['notas'], true);
        }
    }

    // Cargar datos del servicio
    private function loadServiceData($service_id)
    {
        if ($this->serviceData === null) {
            $this->db->query("SELECT name FROM services WHERE id = :service_id LIMIT 1");
            $this->db->bind(':service_id', $service_id);
            $this->serviceData = $this->db->single();

            if (!$this->serviceData) {
                throw new Exception("Servicio no encontrado.");
            }
        }
    }

    // Cargar datos del usuario
    private function loadUserData($company_id)
    {
        if ($this->userData === null) {
            $this->db->query("SELECT name, email FROM users WHERE company_id = :company_id LIMIT 1");
            $this->db->bind(':company_id', $company_id);
            $this->userData = $this->db->single();

            if (!$this->userData) {
                throw new Exception("Usuario no encontrado.");
            }
        }
    }

    // Construir el correo
    public function buildEmail($data, $templateType)
    {
        // Cargar datos de la compañía y del servicio
        $this->loadCompanyData($data['company_id'], $templateType);
        $this->loadServiceData($data['id_service']);

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

        $date = date('d/m/Y', strtotime($data['date']));
        $startTime = date('h:i a', strtotime($data['start_time']));

        $subject_msg = $templateType == 'reserva' ? 'Solicitud de reserva recibida - {fecha_reserva}' : '¡Tu reserva ha sido confirmada! - {fecha_reserva}';
        $subject_msg = str_replace('{fecha_reserva}', $data['date'], $subject_msg);
        $subject = mb_encode_mimeheader($subject_msg, 'UTF-8', 'B', "\n");

        $body = str_replace(
            ['{nombre_cliente}', '{fecha_reserva}', '{hora_reserva}', '{servicio_reservado}', '{notas}', '{ruta_logo}', '{nombre_empresa}'],
            [$data['name'], $data['date'], $startTime, $this->serviceData['name'], $notesHtml, $this->companyData['logo'], $this->companyData['name']],
            $templateContent
        );
        $mailData = ['subject' => $subject, 'body' => $body, 'company_name' => $this->companyData['name']];
        $emailSender = new EmailSender();
        $sender =  $emailSender->sendEmail($data['mail'],  $mailData, ucfirst($templateType));
        if ($templateType == 'reserva') {
            $alertEmailContent = $this->buildAppointmentAlert($data);
            $emailSender->sendEmail($this->userData['email'], $alertEmailContent, null);
        }
        return ['success' => $sender, 'company_name' => $this->companyData['name'], 'social_token' => $this->companyData['social_token']];
    }

    // Otro constructor de correos, como alertas, reutilizando los mismos datos
    public function buildAppointmentAlert($data)
    {

        if ($this->companyData === null || $this->serviceData === null) {
            throw new Exception("Los datos de la compañía y el servicio deben cargarse primero.");
        }

        $this->loadUserData($data['company_id']);

        $alertTemplatePath = $this->baseUrl . 'correos_template/correo_aviso_reserva.php';
        $alertContent = file_get_contents($alertTemplatePath);

        $date = date('d/m/Y', strtotime($data['date']));
        $startTime = date('h:i a', strtotime($data['start_time']));

        $body = str_replace(
            ['{ruta_logo}', '{nombre_usuario}', '{nombre_cliente}', '{telefono_cliente}', '{fecha}', '{hora}', '{nombre_servicio}'],
            [$this->companyData['logo'], $this->userData['name'], $data['name'], $data['phone'], $date, $startTime, $this->serviceData['name']],
            $alertContent
        );

        return ['subject' => 'Alerta de cita', 'body' => $body, 'correo_empresa' => $this->userData['email']];
    }
    public function buildInscriptionAlert($mail)
    {

        $alertTemplatePath = $this->baseUrl . 'correos_template/correo_alerta_inscripcion.php';
        $mailContent = file_get_contents($alertTemplatePath);

        $body = str_replace(
            ['{email_cliente}',],
            [$mail],
            $mailContent
        );

        // Instancia de la clase EmailSender para enviar el correo
        $emailSender = new EmailSender();
        return $emailSender->sendInscriptionAlert('Alerta de inscripción', 'agendaroad@gmail.com', $body);
    }
}