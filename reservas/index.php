<?php
require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/access-token/seguridad/JWTAuth.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__) . '/classes/Appointments.php';
require_once dirname(__DIR__) . '/classes/CompanyController/CompanyController.php';
require_once dirname(__DIR__) . '/classes/Customers.php';

$baseUrl = ConfigUrl::get();

$isDemo = false;

if (isset($_GET['path']) && str_contains($_GET['path'], 'empresa-demo-agendar')) {
    if (isset($_GET['isdemo'])) {
        $isDemo = in_array(strtolower($_GET['isdemo']), ['1', 'true', 'yes']);

        // Si tiene parámetro pero no es válido, redirigir
        if (!$isDemo) {
            header('Location: https://agendarium.com');
            exit;
        }
    } else {
        // Si es ruta demo pero no tiene parámetro, redirigir
        header('Location: https://agendarium.com');
        exit;
    }
}

$url = isset($_GET['path']) ? $_GET['path'] : null;
$view = isset($_GET['view']) ? $_GET['view'] : 'form';
$customerId = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : null;

// Verificar autenticación solo si se proporciona un customer_id
$authenticated = false;
if ($customerId) {
    $auth = new JWTAuth();
    $datosUsuario = $auth->validarTokenUsuario();

    if ($datosUsuario) {
        $authenticated = true;
    } else {
        header("Location: " . $baseUrl . "login/index.php");
        exit();
    }
}

// Crear una instancia del controlador
$controller = new CompanyController();

// Obtener los datos de la empresa
$data = $controller->getCompanyData($url);
$company = $data['company'];
$socialNetworks = $data['socialNetworks'];
$services = $data['services'];
$servicesProvidersCount = $data['servicesProvidersCount'];
$style = $data['style'];

// Si se proporciona un customer_id, obtener los datos del cliente
$customerData = null;
if ($customerId) {
    $customers = new Customers();
    $customerData = $customers->getCustomerById($customerId); // Asegúrate de que este método exista en la clase Customers
}

// Establecer variables de estilo
$primary_color = $style['primary_color'];
$secondary_color = $style['secondary_color'];
$background_color = $style['background_color'];
$button_color = $style['button_color'];
$border_color = $style['border_color'];
$selected_banner = $company['selected_banner'];

$calendar_days_available = $company['calendar_days_available'] ?? $company['fixed_duration'];

// Establecer el título de la página
$pageTitle = ($view === 'details') ? 'Detalles de la Pre-reserva' : 'Reservar Cita';

// Incluir el encabezado
include __DIR__ . '/templates/header.php';

// Renderizar el contenido según la vista
if ($view === 'details') {
    // Obtener detalles de la pre-reserva, por ejemplo, usando 'reservation_id'
    if (isset($_GET['reservation_id'])) {
        $reservation_id = intval($_GET['reservation_id']);
        $appointment = new Appointments();
        $reservation = $appointment->get_appointment($reservation_id);
        if ($reservation) {

            // Aquí deberías obtener los detalles de la reserva desde la base de datos
            $preReserva = [
                'nombre' => $reservation['customer_name'],
                'servicio' => $reservation['service'],
                'fecha' => date('d-m-Y', strtotime($reservation['date'])), // Formato dd-mm-aaaa
                'hora' => date('h:i A', strtotime($reservation['start_time'])), // Formato hh:mm am/pm
                'notas' => $reservation['status'] == 0 ? $company['notas_correo_reserva'] : $company['notas_correo_confirmacion'],
                'estado' => $reservation['status'] == 0 ? 'Pendiente de confirmación' :  'Reserva Confirmada',
                'title'  => $reservation['status'] == 0 ?  'Detalles de Pre-reserva' : 'Reserva Confirmada',

            ];
            // Separar las notas en un array si están en formato de string
            $preReserva['notas'] = json_decode($preReserva['notas'], true);
        } else {
            $preReserva = null;
        }
    } else {
        $preReserva = null;
    }

    // Incluir la plantilla de detalles de pre-reserva
    include __DIR__ . '/templates/detalles_pre_reserva.php';
} else {
    // Incluir la plantilla del formulario de reserva
    include __DIR__ . '/templates/form_reserva.php';
}

// Incluir el pie de página
include __DIR__ . '/templates/footer.php';
