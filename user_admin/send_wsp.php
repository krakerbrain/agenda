<?php


function sendWspReserva($template_name, $telefono, $nombre_cliente,  $fecha_cita, $hora_cita)
{
    $token = $_ENV['WSP_TOKEN'];
    //URL A DONDE SE MANDARA EL MENSAJE
    $url = $_ENV['WSP_URL'];

    $fecha_cita = date('d/m/Y', strtotime($fecha_cita));
    try {
        //CONFIGURACION DEL MENSAJE CON PARÁMETROS
        $mensaje = json_encode([
            "messaging_product" => "whatsapp",
            "to" => $telefono,
            "type" => "template",
            "template" => [
                "name" => $template_name,
                "language" => ["code" => "es"],
                "components" => [
                    [
                        "type" => "header",
                        "parameters" => [
                            [
                                "type" => "image",
                                "image" => [
                                    "link" => "https://agenda2024.online/assets/img/15042021LOGO_2.png"
                                ]
                            ]
                        ]
                    ],
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $nombre_cliente],  // {{1}}
                            ["type" => "text", "text" => $fecha_cita],    // {{3}}
                            ["type" => "text", "text" => $hora_cita]      // {{4}}
                        ]
                    ]
                ]
            ]
        ]);

        //DECLARAMOS LAS CABECERAS
        $header = array("Authorization: Bearer " . $token, "Content-Type: application/json",);
        //INICIAMOS EL CURL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $mensaje);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //OBTENEMOS LA RESPUESTA DEL ENVIO DE INFORMACION
        $response = json_decode(curl_exec($curl), true);
        //IMPRIMIMOS LA RESPUESTA 
        // print_r($response);
        //OBTENEMOS EL CODIGO DE LA RESPUESTA
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //CERRAMOS EL CURL
        curl_close($curl);
        //IMPRIMIMOS EL CODIGO DE LA RESPUESTA
        return $status_code;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
