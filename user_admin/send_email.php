<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function sendEmail($to, $mailContent, $template)
{
    $mail = new PHPMailer(true);
    // Asignar el asunto y el cuerpo del correo
    $mail->Subject = $mailContent['subject'];
    $mail->Body = $mailContent['body'];
    $template = mb_encode_mimeheader($template, 'UTF-8', 'B', "\n");
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'agendaroad@gmail.com'; // Tu dirección de correo de Gmail
        $mail->Password = 'ngua iwiw vogx xkwx'; // Tu contraseña de Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuración del correo electrónico
        if ($template !== "") {
            $mail->setFrom('agendaroad@gmail.com', $mailContent['company_name'] . ' - ' . $template);
            $mail->addAddress($to);
        } else {
            $mail->setFrom('agendaroad@gmail.com', 'ALERTA - Agenda Road');
            $mail->addAddress($mailContent['correo_empresa']);
        }
        $mail->isHTML(true);


        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}
