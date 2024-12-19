<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/UniqueEvents.php';


// Verificar que el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario
    $phone = formatPhoneNumber($_POST['telefono']);
    $data = [
        'event_id' => $_POST['event_id'],
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $phone
    ];

    // Crear una instancia de la clase UniqueEvents
    $uniqueEvents = new UniqueEvents();

    // Llamar al método para registrar una inscripcion
    $result = $uniqueEvents->register_inscription($data);

    // Devolver la respuesta en formato JSON
    echo json_encode($result);
}

function formatPhoneNumber($telefono)
{
    // Eliminar espacios en blanco, guiones, paréntesis y el símbolo "+"
    $telefono = preg_replace('/[\s\-\(\)\+]/', '', $telefono);

    // Si el número empieza con "9" y tiene 8 dígitos (número móvil chileno), agregar "56" al inicio
    if (preg_match('/^9\d{8}$/', $telefono)) {
        return '56' . $telefono;
    }

    // Si el número ya empieza con "56" y tiene 11 dígitos, es correcto
    if (preg_match('/^56\d{9}$/', $telefono)) {
        return $telefono;
    }

    // Si el número ya empieza con "6" y tiene 9 dígitos (número fijo chileno), agregar "56" al inicio
    if (preg_match('/^\d{8}$/', $telefono)) {
        return '569' . $telefono;
    }

    // Si el número no es válido, lanzar una excepción
    throw new Exception('Número de teléfono inválido.');
}