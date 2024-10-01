<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'agendaroad@gmail.com'; // Tu dirección de correo de Gmail
        $this->mail->Password = 'ngua iwiw vogx xkwx'; // Tu contraseña de Gmail
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = 587;
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
            // Devolver true si el correo fue enviado exitosamente
            return true;
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$this->mail->ErrorInfo}";
            return false;
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
}
