<?php
require_once dirname(__DIR__, 2) . '/classes/CompanyModel.php';

$model  = new CompanyModel();

$input = json_decode(file_get_contents('php://input'), true);
$company_id = $input['company_id'];
$user_id = $input['provider_id'];


$services = $model->getServicesByCompanyAndUser($company_id, $user_id);

if ($services) {
    echo json_encode(['success' => true, 'services' => $services]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron servicios.']);
}
