<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';

$auth = new JWTAuth();
$auth->validarTokenUsuario();

// Recibir los datos del formulario
$name = $_POST['business_name'];                            // Nombre del negocio
$phone = $_POST['phone'];                                   // Teléfono
$address = $_POST['address'];                               // Dirección
$logo = isset($_FILES['logo']) ? $_FILES['logo'] : null;    // Logo  

// Crear una instancia de CompanyManager
header('Content-Type: application/json');
$companyManager = new CompanyManager();

// Llamar a la función para crear la empresa con los datos proporcionados
$result = $companyManager->createCompany($name, $phone, $address, $logo);
echo json_encode($result);
