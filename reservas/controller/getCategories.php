<?php
require_once dirname(__DIR__, 2) . '/classes/CompanyModel.php';

$model  = new CompanyModel();

$input = json_decode(file_get_contents('php://input'), true);
$service_id = $input['service_id'];

$categories = $model->getServicesCategories($service_id);

if ($categories) {
    echo json_encode(['success' => true, 'categories' => $categories]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontraron categorías.']);
}
