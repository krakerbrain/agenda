<?php
require_once dirname(__DIR__, 2) . '/configs/init.php';
require_once dirname(__DIR__, 2) . '/classes/Services.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';


try {
    $input = json_decode(file_get_contents('php://input'), true);
    $serviceId = $input['service_id'] ?? null;
    $companyId = $input['company_id'] ?? null;

    if (!$serviceId || !$companyId) {
        throw new Exception('Se requieren service_id y company_id');
    }

    // Instanciar clases
    $usersModel = new Users();
    $servicesModel = new Services($companyId, null); // user_id puede ser null para este caso

    // Obtener datos usando los nuevos métodos
    $providers = $usersModel->getProvidersByService($serviceId, $companyId);
    $service = $servicesModel->getServiceWithDays($serviceId);

    if (!$service) {
        throw new Exception('Servicio no encontrado');
    }

    // Procesar resultados
    $result = [];
    foreach ($providers as $provider) {
        $providerDays = array_fill_keys(explode(',', $provider['provider_days']), true);
        $serviceDays = array_fill_keys(explode(',', $service['available_days']), true);

        $combinedDays = [];
        for ($day = 1; $day <= 7; $day++) {
            $combinedDays[$day] = isset($serviceDays[$day]) && isset($providerDays[$day]);
        }

        $result[] = [
            'id' => $provider['id'],
            'name' => $provider['name'],
            'email' => $provider['email'],
            'photo' => getUserPhotoUrl($provider['id']), // Método ficticio
            'is_active' => (bool)$provider['provider_active'],
            'available_days' => $combinedDays
        ];
    }

    echo json_encode([
        'success' => true,
        'providers' => $result,
        'service' => [
            'id' => $service['id'],
            'name' => $service['name']
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => $e->getTraceAsString() // Solo para desarrollo, quitar en producción
    ]);
}

// Función de ejemplo para fotos de perfil
function getUserPhotoUrl($userId)
{
    return 'https://randomuser.me/api/portraits/men/' . ($userId % 100) . '.jpg';
}
