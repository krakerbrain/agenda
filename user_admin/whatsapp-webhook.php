<?php
require_once dirname(__DIR__) . '/configs/init.php';

// Verificación (ya lo tienes)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $verify_token = "wwwagendariumcom";
    $challenge = $_GET['hub_challenge'];
    $hub_verify_token = $_GET['hub_verify_token'];

    if ($hub_verify_token === $verify_token) {
        echo $challenge; // Responde con el desafío para verificar
    } else {
        http_response_code(403);
        echo "Token de verificación incorrecto.";
    }
}

// Manejo de los mensajes recibidos (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica que el campo 'messages' exista
    if (isset($data['entry'][0]['changes'][0]['value']['messages'])) {
        $messages = $data['entry'][0]['changes'][0]['value']['messages'];

        foreach ($messages as $message) {
            $sender = $message['from']; // Número de teléfono del remitente
            $message_body = $message['text']['body']; // Mensaje enviado

            // Lógica de negocio: procesar el mensaje
            if ($message_body == "reservar cita") {
                // Aquí va la lógica para redirigir el mensaje o procesarlo
                // Por ejemplo, enviar una respuesta automática
                enviarMensaje($sender, "Gracias por tu mensaje. ¿En qué te podemos ayudar?");
            } else {
                // Responder con un mensaje por defecto si no es "reservar cita"
                enviarMensaje($sender, "Recibido, estamos aquí para ayudarte.");
            }
        }
    }

    // Responde correctamente a Meta
    http_response_code(200);
}

// Función para enviar mensajes de WhatsApp (por ejemplo)
function enviarMensaje($recipient, $message)
{
    $access_token = $_ENV['WSP_TOKEN'];
    $phone_number_id = '442457485606672';

    $message_data = [
        'messaging_product' => 'whatsapp',
        'to' => $recipient,
        'text' => [
            'body' => $message
        ]
    ];

    $ch = curl_init('https://graph.facebook.com/v13.0/' . $phone_number_id . '/messages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error en cURL: ' . curl_error($ch);
    }
    curl_close($ch);

    return $response; // Regresar la respuesta de cURL
}
