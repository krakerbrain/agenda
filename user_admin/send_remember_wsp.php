<?php

use Twilio\Rest\Client;

function sendRememberWsp($template, $telefono, $nombre_cliente, $fecha_cita, $hora_cita, $nombre_empresa, $appointment_token)
{
    $sid = $_ENV['TWILIO_ACCOUNT_SID'];
    $token = $_ENV['TWILIO_AUTH_TOKEN'];
    $client = new Client($sid, $token);

    $whatsappSender = $_ENV['TWILIO_WHATSAPP_NUMBER'];

    $templateSid = $template == 'abono_24h' ?  $_ENV['CONTENT_SID_ABONO_24H'] : $_ENV['CONTENT_SID_ABONO_48H']; // nuevo SID para 24h

    try {
        $message = $client->messages->create(
            "whatsapp:" . $telefono,
            [
                "from" => "whatsapp:" . $whatsappSender,
                "contentSid" => $templateSid,
                "contentVariables" => json_encode([
                    "1" => $nombre_cliente,
                    "2" => $nombre_empresa,
                    "3" => $fecha_cita,
                    "4" => $hora_cita,
                    "5" => $appointment_token
                ]),
            ]
        );
        return 200;
    } catch (Exception $e) {
        error_log("Error enviando WhatsApp Twilio: " . $e->getMessage());
        return 400;
    }
}
