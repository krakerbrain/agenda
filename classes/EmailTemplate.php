<?php

require_once 'DatabaseSessionManager.php'; // Asegúrate de tener la ruta correcta

class EmailTemplate
{
    private $conn;

    public function __construct()
    {
        $manager = new DatabaseSessionManager();
        $this->conn = $manager->getDB();
    }

    // Obtener plantillas por company_id
    public function getTemplatesByCompanyId($company_id)
    {
        try {
            $query = $this->conn->prepare("SELECT template_name, subject, body FROM email_templates WHERE company_id = :company_id");
            $query->bindParam(':company_id', $company_id, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Insertar una nueva plantilla
    public function insertTemplate($company_id, $template_name, $subject, $body)
    {
        try {
            $query = $this->conn->prepare("
                INSERT INTO email_templates (company_id, template_name, subject, body)
                VALUES (:company_id, :template_name, :subject, :body)
            ");
            $query->bindParam(':company_id', $company_id, PDO::PARAM_INT);
            $query->bindParam(':template_name', $template_name, PDO::PARAM_STR);
            $query->bindParam(':subject', $subject, PDO::PARAM_STR);
            $query->bindParam(':body', $body, PDO::PARAM_STR);
            $query->execute();
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Actualizar una plantilla existente
    public function updateTemplate($company_id, $template_name, $subject, $body)
    {
        try {
            $query = $this->conn->prepare("
                UPDATE email_templates
                SET subject = :subject, body = :body
                WHERE company_id = :company_id AND template_name = :template_name
            ");
            $query->bindParam(':company_id', $company_id, PDO::PARAM_INT);
            $query->bindParam(':template_name', $template_name, PDO::PARAM_STR);
            $query->bindParam(':subject', $subject, PDO::PARAM_STR);
            $query->bindParam(':body', $body, PDO::PARAM_STR);
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
        if (empty($data['subject'])) {
            $errors[] = 'Asunto es requerido.';
        }
        if (empty($data['body'])) {
            $errors[] = 'Cuerpo del mensaje es requerido.';
        }
        return $errors;
    }

    public function buildEmail($company_id, $templateType, $name, $date, $startTime, $endTime)
    {
        $query = $this->conn->prepare("SELECT subject, body FROM email_templates WHERE company_id = :company_id AND template_name = :template_type LIMIT 1");
        $query->bindParam(':company_id', $company_id);
        $query->bindParam(':template_type', $templateType);
        $query->execute();
        $template = $query->fetch(PDO::FETCH_ASSOC);

        if (!$template) {
            return "Template no encontrado.";
        }

        $subject = $template['subject'];
        $body = $template['body'];
        $subject = str_replace('{fecha_reserva}', $date, $subject);
        // Reemplazar los placeholders en el cuerpo del email
        $body = str_replace(
            ['{nombre_cliente}', '{fecha_reserva}', '{start_time}',],
            [$name, $date, $startTime],
            $body
        );
        return ['subject' => $subject, 'body' => $body];
    }
}