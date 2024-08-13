<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendConfirmationEmail($name, $email, $date, $time)
{
    $mail = new PHPMailer(true);

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
        $mail->setFrom('agendaroad@gmail.com', 'Agenda Road');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de cita';
        $mail->Body = "Hola $name,<br><br>Tu cita ha sido confirmada para el $date a las $time.<br><br>Gracias.";

        // Enviar el correo electrónico
        $mail->send();
    } catch (Exception $e) {
        throw new Exception("Error al enviar el correo: " . $email . " {$mail->ErrorInfo}");
    }
}


function sendEmail($to, $mailContent)
{
    $mail = new PHPMailer(true);
    $mail->Subject = $mailContent['subject'];
    $mail->Body = $mailContent['body'];

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
        $mail->setFrom('agendaroad@gmail.com', 'Agenda Road');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $mail->Subject;
        $mail->Body = $mail->Body;

        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}