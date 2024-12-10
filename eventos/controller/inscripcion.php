<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/UniqueEvents.php';


// Verificar que el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario

    $data = [
        'event_id' => $_POST['event_id'],
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['telefono']
    ];

    // Crear una instancia de la clase UniqueEvents
    $uniqueEvents = new UniqueEvents();

    // Llamar al método para registrar una inscripcion
    $result = $uniqueEvents->register_inscription($data);

    // Devolver la respuesta en formato JSON
    echo json_encode($result);
}
