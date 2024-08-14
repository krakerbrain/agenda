<?php

require_once 'DatabaseSessionManager.php'; // Asegúrate de tener la ruta correcta
require_once 'ConfigUrl.php'; // Asegúrate de tener la ruta correcta

class EmailTemplate
{
    private $conn;

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
            $query = $this->conn->prepare("SELECT template_name, notas FROM email_templates WHERE company_id = :company_id");
            $query->bindParam(':company_id', $company_id, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Insertar una nueva plantilla
    public function insertTemplate($company_id, $template_name, $notas)
    {
        try {
            $query = $this->conn->prepare("
            INSERT INTO email_templates (company_id, template_name, notas)
            VALUES (:company_id, :template_name, :notas)
        ");
            // Almacenar el JSON en una variable antes de pasarlo a bindParam
            $notasJson = json_encode($notas);
            $query->bindParam(':company_id', $company_id, PDO::PARAM_INT);
            $query->bindParam(':template_name', $template_name, PDO::PARAM_STR);
            $query->bindParam(':notas',  $notasJson, PDO::PARAM_STR);
            $query->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    // Actualizar una plantilla existente
    public function updateTemplate($company_id, $template_name, $notas)
    {
        try {
            $query = $this->conn->prepare("
            UPDATE email_templates
            SET notas = :notas
            WHERE company_id = :company_id AND template_name = :template_name
        ");

            // Almacenar el JSON en una variable antes de pasarlo a bindParam
            $notasJson = json_encode($notas);
            $query->bindParam(':company_id', $company_id, PDO::PARAM_INT);
            $query->bindParam(':template_name', $template_name, PDO::PARAM_STR);
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

    public function buildEmail($company_id, $templateType, $service_id, $name, $date, $startTime)
    {
        // Obtener el asunto de la tabla email_templates
        $query = $this->conn->prepare("SELECT subject, notas FROM email_templates WHERE company_id = :company_id AND template_name = :template_type LIMIT 1");
        $query->bindParam(':company_id', $company_id);
        $query->bindParam(':template_type', $templateType);
        $query->execute();
        $template = $query->fetch(PDO::FETCH_ASSOC);

        if (!$template) {
            return "Template no encontrado.";
        }

        // Obtener el nombre y logo de la empresa
        $companyQuery = $this->conn->prepare("SELECT name, logo FROM companies WHERE id = :company_id LIMIT 1");
        $companyQuery->bindParam(':company_id', $company_id);
        $companyQuery->execute();
        $company = $companyQuery->fetch(PDO::FETCH_ASSOC);
        $logo = 'https://agenda2024.online/' . $company['logo'];
        if (!$company) {
            return "Empresa no encontrada.";
        }

        // Obtener el nombre del servicio
        $serviceQuery = $this->conn->prepare("SELECT name FROM services WHERE id = :service_id LIMIT 1");
        $serviceQuery->bindParam(':service_id', $service_id);
        $serviceQuery->execute();
        $service = $serviceQuery->fetch(PDO::FETCH_ASSOC);

        if (!$service) {
            return "Servicio no encontrado.";
        }

        // Decodificar el JSON de notas
        $notes = json_decode($template['notas'], true);

        // Construir el HTML de las notas
        $notesHtml = '';
        if (!empty($notes)) {
            foreach ($notes as $note) {
                $notesHtml .= "<li>{$note}</li>";
            }
        } else {
            $notesHtml = '<li>No hay notas adicionales.</li>';
        }

        // Leer la plantilla desde el archivo
        $templatePath = $this->baseUrl . 'correos_template/correo_confirmacion.php';
        $templateContent = file_get_contents($templatePath);

        // Reemplazar los placeholders en el asunto
        $subject = str_replace('{fecha_reserva}', $date, $template['subject']);

        // Reemplazar los placeholders en el cuerpo del email
        $body = str_replace(
            ['{nombre_cliente}', '{fecha_reserva}', '{hora_reserva}', '{servicio_reservado}', '{notas}', '{ruta_logo}', '{nombre_empresa}'],
            [$name, $date, $startTime, $service['name'], $notesHtml, $logo, $company['name']],
            $templateContent
        );

        return ['subject' => $subject, 'body' => $body];
    }
}
