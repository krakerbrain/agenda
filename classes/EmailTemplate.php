<?php

require_once 'Database.php';
require_once 'ConfigUrl.php';
require_once 'EmailSender.php';
require_once 'NotificationLog.php';
require_once 'EmailDataLoader.php';
require_once 'EmailBuilder.php';


class EmailTemplate
{
    private $db;
    private $baseUrl;
    private $dataLoader;
    private $emailBuilder;
    private $emailSender;


    public function __construct($db = null)
    {
        $baseUrl = new ConfigUrl();
        $this->baseUrl = $baseUrl->get();
        if ($db) {
            $this->db = $db;
        } else {
            $this->db = new Database();
        }
        $this->dataLoader = new EmailDataLoader($this->db);
        $this->emailBuilder = new EmailBuilder($this->baseUrl);
        $this->emailSender = new EmailSender();
    }

    public function getTemplatesForMail($identifier, $template_name, $table)
    {
        $template = "notas_correo_" . $template_name;
        try {
            // Preparar y ejecutar la consulta usando la instancia de Database
            $this->db->query("SELECT $template as nota_correo FROM $table WHERE id = :identifier");
            $this->db->bind(':identifier', $identifier);
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
    public function updateTemplate($identifier, $template_name, $table, $notas)
    {
        $template = "notas_correo_" . $template_name;
        try {
            // Sanitizar las notas
            $notas = $this->sanitize($notas);

            // Almacenar el JSON en una variable antes de pasarlo a bindParam
            $notasJson = json_encode($notas);

            // Preparar y ejecutar la consulta
            $this->db->query("UPDATE $table SET $template = :notas WHERE id = :identifier");
            $this->db->bind(':identifier', $identifier);
            $this->db->bind(':notas', $notasJson);
            $this->db->execute();

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
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
        try {
            // Cargar datos
            $companyData = $this->dataLoader->getCompanyData($data['company_id'], $templateType);
            $serviceData = $this->dataLoader->getServiceData($data['id_service']);

            // Construir cuerpo del correo
            $placeholders = [
                '{nombre_cliente}' => $data['customer_name'],
                '{fecha_reserva}' => date('d/m/Y', strtotime($data['date'])),
                '{hora_reserva}' => date('h:i a', strtotime($data['start_time'])),
                '{servicio_reservado}' => $serviceData['name'],
                '{notas}' => $this->formatNotes($companyData['notas']),
                '{ruta_logo}' => $companyData['logo'],
                '{nombre_empresa}' => $companyData['name']
            ];

            $body = $this->emailBuilder->buildTemplate($templateType, $placeholders);

            // Enviar correo
            $subject = $this->buildSubject($templateType, $data['date']);

            if ($templateType == 'reserva') {
                $alertEmailContent = $this->buildAppointmentAlert($data, $companyData, $serviceData);
                $alertSubject = $alertEmailContent['subject'];
                $this->emailSender->sendEmail($alertSubject, $alertEmailContent, null);
            }
            $success = $this->emailSender->sendEmail($data['customer_mail'], ['subject' => $subject, 'body' => $body, 'company_name' => $companyData['name']], ucfirst($templateType));

            return ['success' => $success, 'company_name' => $companyData['name'], 'social_token' => $companyData['social_token']];
        } catch (Exception $e) {
            throw new Exception("Error al construir/enviar correo: " . $e->getMessage());
        }
    }

    private function buildSubject($templateType, $date)
    {
        $subjectTemplates = [
            'reserva' => 'Solicitud de reserva recibida - {fecha_reserva}',
            'confirmacion' => '¡Tu reserva ha sido confirmada! - {fecha_reserva}'
        ];
        $subject = str_replace('{fecha_reserva}', $date, $subjectTemplates[$templateType] ?? 'Notificación');

        // Codificar el asunto en UTF-8
        return mb_encode_mimeheader($subject, 'UTF-8', 'B', "\n");
    }

    private function formatNotes($notes)
    {
        if (empty($notes)) {
            return '<li>No hay notas adicionales.</li>';
        }
        return implode('', array_map(fn($note) => "<li>{$note}</li>", $notes));
    }

    public function buildAppointmentAlert($data, $companyData, $serviceData)
    {
        try {

            $userData = $this->dataLoader->getUserData($data['company_id']);
            // Construir placeholders para el correo de alerta
            $placeholders = [
                '{nombre_cliente}' => $data['customer_name'],
                '{fecha}' => date('d/m/Y', strtotime($data['date'])),
                '{hora}' => date('h:i a', strtotime($data['start_time'])),
                '{nombre_servicio}' => $serviceData['name'],
                '{telefono_cliente}' => $data['customer_phone'],
                '{ruta_logo}' => $companyData['logo'],
                '{nombre_usuario}' => $userData['name'],
            ];

            // Construir contenido del correo usando plantilla de alerta
            $alertBody = $this->emailBuilder->buildTemplate('aviso_reserva', $placeholders);

            // Construir asunto
            $alertSubject = 'Alerta de cita';

            // Devolver los datos del correo de alerta
            return [
                'subject' => $alertSubject,
                'body' => $alertBody,
                'subject' => $userData['email'],
            ];
        } catch (Exception $e) {
            error_log("Error al construir correo de alerta: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function buildInscriptionAlert($mail)
    {

        $placeholders = [
            '{email_cliente}' => $mail
        ];

        $inscriptionAlertBody = $this->emailBuilder->buildTemplate('alerta_inscripcion', $placeholders);
        $inscriptionAlertSubject = 'Alerta de inscripción';

        return $this->emailSender->sendInscriptionAlert($inscriptionAlertSubject, 'agendaroad@gmail.com', $inscriptionAlertBody);
    }

    public function buildInscriptionMail($company_id)
    {
        $userData = $this->dataLoader->getUserData($company_id);

        $placeholders = [
            '{nombre_cliente}' => $userData['name'],
            '{login_link}' => $this->baseUrl . 'login',
            '{email_cliente}' => $userData['email']
        ];

        $inscriptionBody = $this->emailBuilder->buildTemplate('inscripcion', $placeholders);
        $inscriptionSubject = 'Bienvenido a Agendarium';

        return $this->emailSender->sendInscriptionAlert($inscriptionSubject, $userData['email'], $inscriptionBody);
    }

    public function buildEventMail($data, $templateType)
    {

        try {
            $companyData = $this->dataLoader->getCompanyData($data['company_id'], $templateType);
            $notas = $templateType == 'reserva' ?  json_decode($data['notas_reserva'], true) : json_decode($data['notas_confirmacion'], true);

            // Construir cuerpo del correo
            $placeholders = [
                '{nombre_cliente}' => $data['participant_name'],
                '{fecha_reserva}' => date('d/m/Y', strtotime($data['event_date'])),
                '{hora_reserva}' => date('h:i a', strtotime($data['event_start_time'])),
                '{evento}' => $data['event_name'],
                '{notas}' => $this->formatNotes($notas),
                '{ruta_logo}' => $companyData['logo'],
                '{nombre_empresa}' => $companyData['name']
            ];
            $templateType = $templateType . '_evento';
            $body = $this->emailBuilder->buildTemplate($templateType, $placeholders);

            $subject = $this->buildSubject($templateType, $data['event_date']);

            $success = $this->emailSender->sendEmail($data['email'], ['subject' => $subject, 'body' => $body, 'company_name' => $companyData['name']]);
            return ['success' => $success, 'company_name' => $companyData['name']];
        } catch (Exception $e) {
            error_log("Error al construir/enviar correo: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    // construir correo para avisar a la emporesa que un usuario bloqueado intento hacer una cita

    public function buildBlockedUserAlertMail($data)
    {
        $companyData = $this->dataLoader->getCompanyData($data['company_id'], 'reserva');
        $userData = $this->dataLoader->getUserData($data['company_id']);
        $placeholders = [
            '{empresa}' => $companyData['name'],
            '{nombre_cliente}' => $data['name'],
            '{telefono_cliente}' => $data['phone'],
            '{email_cliente}' => $data['mail'],
            // fecha intento hoy
            '{fecha_intento}' => date('d/m/Y H:i:s')
        ];
        $alertBody = $this->emailBuilder->buildTemplate('alerta_usuario_bloqueado', $placeholders);
        $alertSubject = 'Alerta: Cliente bloqueado intentó hacer una cita';

        // return $this->emailSender->sendEmail($userData['mail'], ['subject' => $alertSubject, 'body' => $alertBody, 'company_name' => $companyData['name']]);
        return $this->emailSender->sendEmail('marioplantabaja@gmail.com', ['subject' => $alertSubject, 'body' => $alertBody, 'company_name' => $companyData['name']]);
    }

    public function sendSuperAdminPasswordRecovery(array $data): array
    {
        try {
            $placeholders = [
                '{nombre_usuario}' => $data['user_name'],
                '{enlace_recuperacion}' => $data['reset_link'],
                '{tiempo_expiracion}' => $data['expiration_time'],
                '{nombre_empresa}' => 'Agendarium.com',
                '{user_email}' => $data['user_email'],
                '{year}' => date('Y')
            ];

            $body = $this->emailBuilder->buildTemplate('recuperacion_contrasenia', $placeholders);
            $subject = "Recuperación de contraseña - Agendarium.com";

            $sent = $this->emailSender->sendEmail(
                $data['user_email'],
                [
                    'subject' => $subject,
                    'body' => $body,
                    'company_name' => 'Agendarium.com'
                ],
                'password_recovery'
            );

            if (!$sent) {
                throw new Exception("Error al enviar el correo electrónico.");
            }

            return ['success' => true, 'message' => 'Correo enviado correctamente'];
        } catch (Exception $e) {
            error_log("Error en sendSuperAdminPasswordRecovery: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendCompanyPasswordRecovery(array $data): array
    {
        try {
            $companyData = $this->dataLoader->getCompanyData($data['company_id']);

            $placeholders = [
                '{nombre_usuario}' => $data['user_name'],
                '{enlace_recuperacion}' => $data['reset_link'],
                '{tiempo_expiracion}' => $data['expiration_time'],
                '{ruta_logo}' => $companyData['logo'],
                '{nombre_empresa}' => $companyData['name'],
                '{user_email}' => $data['user_email'],
                '{year}' => date('Y')
            ];

            $body = $this->emailBuilder->buildTemplate('recuperacion_contrasenia', $placeholders);
            $subject = "Recuperación de contraseña - " . $companyData['name'];

            $sent = $this->emailSender->sendEmail(
                $data['user_email'],
                [
                    'subject' => $subject,
                    'body' => $body,
                    'company_name' => $companyData['name']
                ],
                'password_recovery'
            );

            if (!$sent) {
                throw new Exception("Error al enviar el correo electrónico.");
            }

            return ['success' => true, 'message' => 'Correo enviado correctamente'];
        } catch (Exception $e) {
            error_log("Error en sendCompanyPasswordRecovery: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
