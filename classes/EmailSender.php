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
        try {
            // Si se pasa un template personalizado
            if ($template !== '') {
                $this->mail->setFrom('agendaroad@gmail.com', $mailContent['company_name'] . ' - ' . $template);
            } else {
                $this->mail->setFrom('agendaroad@gmail.com', 'ALERTA - Agendarium');
            }

            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $mailContent['subject'];
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
    public function sendInscriptionMail($subject, $to, $mailContent)
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
}
