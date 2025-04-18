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

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

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
            } else if ($_GET['action'] === 'getCustomerIncidents') {
                $customerId = $_GET['id'];
                $incidents = $customer->getCustomerIncidents($customerId, $company_id);

                if ($incidents !== false) {
                    echo json_encode(['success' => true, 'data' => $incidents]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al obtener incidencias']);
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
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'updateCustomer') {
            if ($_POST['action'] === 'updateCustomer') {
                $customerId = $_POST['id'];
                $name = $_POST['name'];
                $phone = $_POST['phone'];
                $mail = $_POST['mail'];
                $blocked = isset($_POST['blocked']) ? 1 : 0;
                $notes = !empty($_POST['notes']) ? trim($_POST['notes']) : NULL;

                // Actualizar el cliente
                $result = $customer->updateCustomer($customerId, $name, $phone, $mail, $blocked, $notes);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Cliente actualizado correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al actualizar el cliente']);
                }
                exit;
            }
        }
        if ($_GET['action'] === 'blockCustomer') {

            $data = json_decode(file_get_contents('php://input'), true);
            $customerId = $data['customer_id'];
            $nota = $data['nota'];

            // Obtener la custom_url de la empresa
            $result = $customer->toggleBlockCustomer($customerId, $nota);
            echo json_encode($result);
            exit;
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if ($data['action'] == 'deleteCustomer') {
                $customerId = $data['customer_id'];
                $result = $customer->deleteCustomer($customerId);
                echo json_encode(["success" => $result, "message" => "Cliente eliminado correctamente"]);
                exit;
            } else if ($data['action'] == 'deleteIncidents') {
                $customerId = $data['customer_id'];
                $incidentIds = $data['incidents'];

                $result = $customer->deleteIncidents($incidentIds, $customerId);
                echo json_encode(["success" => $result, "message" => "Incidencias eliminadas correctamente"]);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    } else {
        // Manejo de otros métodos (si es necesario)
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
