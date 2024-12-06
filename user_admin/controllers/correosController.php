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
$tipo = $input['tipo'] ?? '';
$table = $input['table'] ?? '';
$eventId = $input['eventId'] ?? null;
$response = ['success' => false, 'message' => 'Acción no válida.'];
// Ajustar el identificador dependiendo del tipo de consulta
$identifier = ($table === 'unique_events' && !empty($eventId)) ? $eventId : $company_id;

switch ($action) {
    case 'getTemplates':
        if (!empty($company_id) && is_numeric($company_id)) {
            $emailTemplate = new EmailTemplate();


            // Usar el mismo método pero pasando el identificador adecuado
            $result = $emailTemplate->getTemplatesForMail($identifier, $tipo, $table);

            $response = ['success' => true, 'data' => $result];
        } else {
            $response['message'] = 'ID de compañía no válido.';
        }
        break;

    case 'saveTemplate':
        // Asume que los datos llegan en el cuerpo de la solicitud JSON
        $notas = $input['notas'] ?? [];
        if ($tipo) { // Asume que si template_name existe, es una actualización
            $emailTemplate = new EmailTemplate();
            $result = $emailTemplate->updateTemplate($identifier, $tipo, $table, $notas);
        }
        $response = $result;

        break;

    default:
        $response['message'] = 'Acción no reconocida.';
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
