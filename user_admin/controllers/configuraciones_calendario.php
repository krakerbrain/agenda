<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';

$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
$companyManager = new CompanyManager();
$company_id = $datosUsuario['company_id'];


// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    // Verificar que el campo 'action' esté presente y sea igual a 'newPeriod'
    if (isset($input['action']) && $input['action'] === 'newPeriod') {


        $diasPeriodo = $input['dias'];

        try {

            // Obtener el fixed_start_date actual
            $currentFixedStartDate = $companyManager->getFixedStartDate($company_id);
            if ($currentFixedStartDate) {
                // Convertir la fecha de inicio actual a un objeto DateTime
                $newStartDay = new DateTime($currentFixedStartDate);

                // Sumar los días del nuevo periodo para establecer la nueva fecha de inicio
                $newStartDay->modify("+$diasPeriodo days");

                // Actualizar el fixed_start_date con el nuevo periodo
                $result = $companyManager->updateFixedStartDay($company_id, $newStartDay);

                // Verificar el resultado y enviar la respuesta
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Nuevo periodo creado exitosamente']);
                } else {
                    throw new Exception("No se pudo actualizar la fecha de inicio");
                }
            } else {
                throw new Exception("No se encontró la fecha de inicio actual");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
