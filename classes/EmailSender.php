<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/configs/init.php';

class EmailSender
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }

    // Configuración del servidor SMTP
    private function configureSMTP()
    {

        $this->mail->isSMTP();
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $_ENV["SMTP_USER"]; // Tu dirección de correo de Gmail
        $this->mail->Password = $_ENV["SMTP_PASS"]; // Tu contraseña de Gmail
        if ($_ENV["APP_ENV"] == 'local') {
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port = 465;
            $this->mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        } else {
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = 587;
        }
    }

    // Método común para enviar el correo
    public function sendEmail($to, $mailContent, $template = '')
    {
        $template = mb_encode_mimeheader($template, 'UTF-8', 'B', "\n");
        $encodedSubject = mb_encode_mimeheader($mailContent['subject'], 'UTF-8', 'B', "\n");
        try {
            // Si se pasa un template personalizado
            if ($template !== '') {
                $this->mail->setFrom('agendaroad@gmail.com', $mailContent['company_name'] . ' - ' . $template);
            } else {
                $this->mail->setFrom('agendaroad@gmail.com', 'ALERTA - Agendarium');
            }

            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $encodedSubject;
            $this->mail->Body = $mailContent['body'];

            $this->mail->send();
            // Limpiar direcciones después del envío
            $this->mail->clearAddresses();

            // Devolver true si el correo fue enviado exitosamente
            return true;
        } catch (Exception $e) {
            // echo "Error al enviar el correo: {$this->mail->ErrorInfo}";
            // return false;
            throw new Exception("Error al enviar el correo: {$this->mail->ErrorInfo}");
        }
    }

    // Método para enviar correos de alerta de inscripcion
    public function sendInscriptionAlert($subject, $to, $mailContent)
    {
        try {
            $this->mail->setFrom('agendaroad@gmail.com', 'INSCRIPCION - Agendarium');
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $mailContent;

            $this->mail->send();
            // Devolver true si el correo fue enviado exitosamente
            return true;
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$this->mail->ErrorInfo}";
            return false;
        }
    }
    // Método para enviar correos de alerta de inscripcion
    public function sendStandardEmail($subject, $to, $mailContent)
    {
        $encodedSubject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\n");
        try {
            $this->mail->setFrom('agendaroad@gmail.com', 'INSCRIPCION - Agendarium');
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $encodedSubject;
            $this->mail->Body = $mailContent;

            $this->mail->send();
            // Devolver true si el correo fue enviado exitosamente
            return true;
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$this->mail->ErrorInfo}";
            return false;
        }
    }

    public function sendContactEmail($mailContent)
    {
        try {
            $this->mail->setFrom('agendaroad@gmail.com', 'SOPORTE - Agendarium');

            $this->mail->addReplyTo($mailContent['from'], $mailContent['from_name']);
            $this->mail->addAddress($_ENV['CONTACT_RECIPIENT_EMAIL'] ?? 'soporte@agendarium.com');

            // Opcional: CC o BCC si es necesario
            if (!empty($_ENV['CONTACT_CC_EMAILS'])) {
                $ccEmails = explode(',', $_ENV['CONTACT_CC_EMAILS']);
                foreach ($ccEmails as $ccEmail) {
                    $this->mail->addCC(trim($ccEmail));
                }
            }

            $this->mail->isHTML(true);
            $this->mail->Subject = mb_encode_mimeheader($mailContent['subject'], 'UTF-8');
            $this->mail->Body = $mailContent['body'];
            $this->mail->AltBody = $this->createTextVersion($mailContent['body']);

            if (!$this->mail->send()) {
                throw new Exception('No se pudo enviar el email: ' . $this->mail->ErrorInfo);
            }

            return true;
        } catch (Exception $e) {
            error_log("EmailSender Error: " . $e->getMessage());
            throw $e;
        }
    }

    private function createTextVersion($htmlContent)
    {
        // Conversión simple de HTML a texto plano
        $text = strip_tags($htmlContent);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
