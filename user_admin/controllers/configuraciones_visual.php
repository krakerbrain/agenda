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


    // Aquí puedes añadir el resto de la lógica común
    $schedule_mode = $_POST['schedule_mode'];
    $calendar_mode = $_POST['calendar_mode'];
    $blocked_dates = implode(',', $_POST['blocked_dates']);
    $bg_color = $_POST['background-color'];
    $font_color = $_POST['font-color'];
    $btn1_color = $_POST['btn-primary-color'];
    $btn2_color = $_POST['btn-secondary-color'];

    $data = [
        'company_id' => $company_id,
        'schedule_mode' => $schedule_mode,
        'calendar_mode' => $calendar_mode,
        'blocked_dates' => $blocked_dates,
        'bg_color' => $bg_color,
        'font_color' => $font_color,
        'btn1_color' => $btn1_color,
        'btn2_color' => $btn2_color,
        'calendar_days_available' => null,
        'fixed_start_date' => null,
        'fixed_duration' => null,
        'auto_open' => 0
    ];

    if ($calendar_mode === 'corrido') {
        $data['calendar_days_available'] = $_POST['calendar_days_available'];
    } else {
        $data['fixed_start_date'] = $_POST['fixed_start_date'];
        $data['fixed_duration'] = $_POST['fixed_duration'];
        $data['auto_open'] = isset($_POST['auto_open']) ? 1 : 0;
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
