<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/classes/EmailTemplate.php';

$baseUrl = ConfigUrl::get();
$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();

$company_id = $datosUsuario['company_id'] ?? null;

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$response = ['success' => false, 'message' => 'Acción no válida.'];

switch ($action) {
    case 'getTemplates':
        if (!empty($company_id) && is_numeric($company_id)) {
            $emailTemplate = new EmailTemplate();
            $result = $emailTemplate->getTemplatesByCompanyId($company_id);
            $response = ['success' => true, 'data' => $result];
        } else {
            $response['message'] = 'ID de compañía no válido.';
        }
        break;

    case 'saveTemplate':
        // Asume que los datos llegan en el cuerpo de la solicitud JSON
        $template_name = $input['template_name'] ?? '';
        $notas = $input['notas'] ?? [];


        $emailTemplate = new EmailTemplate();
        if ($template_name) { // Asume que si template_name existe, es una actualización
            $result = $emailTemplate->updateTemplate($company_id, $template_name, $notas);
        }
        $response = $result;

        break;

    default:
        $response['message'] = 'Acción no reconocida.';
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
