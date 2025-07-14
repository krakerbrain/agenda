<?php

use Twilio\Rest\Client;

function sendWspReserva($template_name, $telefono, $nombre_cliente,  $fecha_cita, $hora_cita, $nombre_empresa, $appointment_token, $evento)
{
    $sid = $_ENV['TWILIO_ACCOUNT_SID'];
    $token = $_ENV['TWILIO_AUTH_TOKEN'];
    $client = new Client($sid, $token);

    $whatsappSender = $_ENV['TWILIO_WHATSAPP_NUMBER']; // Ej: +56979600605s

    $templateSid = $template_name === 'aviso_reserva' ? $_ENV['CONTENT_SID_RESERVA'] : $_ENV['CONTENT_SID_CONFIRMACION'];

    try {
        $message = $client->messages->create(
            "whatsapp:" . $telefono,
            [
                "from" => "whatsapp:" . $whatsappSender,
                "contentSid" => $templateSid,
                "contentVariables" => json_encode([
                    "1" => $nombre_empresa,
                    "2" => $nombre_cliente,
                    "3" => $evento,
                    "4" => $fecha_cita,
                    "5" => $hora_cita,
                    "6" => $appointment_token,
                ]),
            ]
        );
        return 200; // éxito
    } catch (Exception $e) {
        error_log("Error enviando WhatsApp Twilio: " . $e->getMessage());
        return 400;
    }
}
