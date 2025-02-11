<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';

$auth = new JWTAuth();
$datosUsuario = $auth->validarTokenUsuario();
$companyManager = new CompanyManager();
$company_id = $datosUsuario['company_id'];

function validateInputs($data)
{
    $errors = [];

    // Verificar si los campos necesarios están presentes y no vacíos
    if (empty($data['schedule_mode'])) {
        $errors[] = 'El modo de horario es requerido.';
    }

    if (empty($data['calendar_mode'])) {
        $errors[] = 'El modo de calendario es requerido.';
    }

    if ($data['calendar_mode'] === 'corrido' && empty($data['calendar_days_available'])) {
        $errors[] = 'Los días disponibles del calendario son requeridos.';
    } elseif ($data['calendar_mode'] !== 'corrido') {
        if (empty($data['fixed_start_date'])) {
            $errors[] = 'La fecha de inicio fija es requerida.';
        }
        if (empty($data['fixed_duration'])) {
            $errors[] = 'La duración del periodo es requerida.';
        }
    }

    // Retornar los errores encontrados, si hay
    return $errors;
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // Aquí puedes añadir el resto de la lógica común
    $schedule_mode = $_POST['schedule_mode'];
    $calendar_mode = $_POST['calendar_mode'];
    $bg_color = $_POST['background-color'];
    $font_color = $_POST['font-color'];
    $btn1_color = $_POST['btn-primary-color'];
    $btn2_color = $_POST['btn-secondary-color'];
    $time_step = $_POST['time_step'];
    $time_step_value = $_POST['time_step_value'];

    $data = [
        'company_id' => $company_id,
        'schedule_mode' => $schedule_mode,
        'calendar_mode' => $calendar_mode,
        'bg_color' => $bg_color,
        'font_color' => $font_color,
        'btn1_color' => $btn1_color,
        'btn2_color' => $btn2_color,
        'calendar_days_available' => null,
        'fixed_start_date' => null,
        'fixed_duration' => null,
        'auto_open' => 0,
        'time_step' => empty($time_step) ? null : $time_step_value, // Asigna null si está vacío, sino toma el valor de time_step_value
    ];

    if ($calendar_mode === 'corrido') {
        $data['calendar_days_available'] = $_POST['calendar_days_available'];
    } else {
        $data['fixed_start_date'] = $_POST['fixed_start_date'];
        $data['fixed_duration'] = $_POST['fixed_duration'];
        $data['auto_open'] = isset($_POST['auto_open']) ? 1 : 0;
    }

    // Validar los inputs
    $validationErrors = validateInputs($data);
    // Verificar si hay errores de validación
    if (!empty($validationErrors)) {
        echo json_encode(['success' => false, 'errors' => $validationErrors]);
        exit; // Termina el script si hay errores
    }
    try {
        $result = $companyManager->updateCompanyConfig($data);

        if ($result['success']) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception($result['error']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
