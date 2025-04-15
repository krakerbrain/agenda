<?php
require_once dirname(__DIR__, 2) . '/classes/HoursAvailabilityManager.php';
// Procesar solicitud
$data = json_decode(file_get_contents('php://input'), true);
$date = $data['date'];
$service_id = $data['service_id'];
$company_id = $data['company_id'];
$user_id = $data['provider'];
$debugMode = isset($_GET['debug']) && $_GET['debug'] === 'true';

$availabilityManager = new HoursAvailabilityManager($company_id, $user_id, $debugMode);
$response = $availabilityManager->getAvailableHours($date, $service_id, $company_id);

echo json_encode($response);
