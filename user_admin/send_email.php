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
        $mail->Username = 'krakerbrain@gmail.com'; // Tu dirección de correo de Gmail
        $mail->Password = 'xfem ehce lpps owrj'; // Tu contraseña de Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuración del correo electrónico
        $mail->setFrom('krakerbrain@gmail.com', 'Mario Montenegro');
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
