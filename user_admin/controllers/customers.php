<?php

use Google\Service\CloudControlsPartnerService\Customer;

require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/Customers.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyModel.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

try {
    $company_id = $datosUsuario['company_id'];
    $customer = new Customers;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['action'])) {
            if ($_GET['action'] === 'getCustomerDetail') {
                $customerId = $_GET['id'];
                $customerDetails = $customer->getCustomerDetail($customerId, $company_id);

                if ($customerDetails) {
                    echo json_encode(['success' => true, 'data' => $customerDetails]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
                }
                exit;
            } else if ($_GET['action'] === 'getUrl') {

                // Crear una instancia de Company_Model
                $companyModel = new CompanyModel();

                // Obtener la custom_url de la empresa
                $customUrl = $companyModel->getCustomUrl($company_id);

                if ($customUrl) {
                    echo json_encode(['success' => true, 'custom_url' => $customUrl]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró la URL de la empresa.']);
                }
                exit;
            }
        } else {
            $status = isset($_GET['status']) ? $_GET['status'] : 'all';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10; // Número de registros por página
            $offset = ($page - 1) * $limit;

            $customersData = $customer->get_paginated_customers($company_id, $status, $offset, $limit);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $customersData]);
        }
    } else {
        // Manejo de otros métodos (si es necesario)
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
