<?php
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/Users.php';
require_once dirname(__DIR__, 2) . '/classes/EmailTemplate.php';
require_once dirname(__DIR__, 2) . '/classes/Schedules.php';

header('Content-Type: application/json'); // Asegura que la respuesta sea JSON

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Recibir los datos del formulario
        $name = $_POST['business_name'];                            // Nombre del negocio
        $phone = $_POST['phone'];                                   // Teléfono
        $address = $_POST['address'];                               // Dirección
        $logo = isset($_FILES['logo']) ? $_FILES['logo'] : null;    // Logo
        $owner_name = $_POST['owner_name'];                         // Nombre del propietario
        $email = $_POST['email'];                                   // Email del propietario
        $password = '1234';                                         // Contraseña temporal
        $role_id = 2;                                               // Rol predeterminado

        // Crear una instancia de CompanyManager
        $companyManager = new CompanyManager();

        // Llamar a la función para crear la empresa con los datos proporcionados
        $result = $companyManager->createCompany($name, $phone, $address, $logo);

        // Verificar el resultado y responder según corresponda
        if ($result['success']) {
            // Empresa creada con éxito, continuar con el registro del usuario
            $company_id = $result['company_id'];

            // Crear una instancia de la clase User
            $user = new Users();

            // Preparar los datos para registrar al usuario
            $userData = [
                'username' => $owner_name,
                'email' => $email,
                'password' => $password,
                'password2' => $password, // Duplicamos porque tu método valida ambas contraseñas
                'company_id' => $company_id,
                'role_id' => $role_id
            ];

            // Llamar a la función para registrar al usuario
            $register_result = $user->register_user($userData);

            // Verificar si el registro fue exitoso
            if ($register_result['success']) {
                $new_user_id = $register_result['user_id'];
                $user_schedule = new Schedules($company_id, $new_user_id);
                $user_schedule->addNewSchedule();

                $emailTemplateBuilder = new EmailTemplate();
                $emailSent = $emailTemplateBuilder->buildInscriptionAlert($userData['email']);

                if ($emailSent) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Empresa creada exitosamente. Usuario registrado con éxito. Te enviaremos un correo para que actives tu cuenta. Bienvenido a Agendarium."
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => "El registro fue exitoso, pero hubo un problema al enviar el correo de activación."
                    ]);
                }
            } else {
                // Error al registrar el usuario
                echo json_encode([
                    'success' => false,
                    'message' => "Empresa creada, pero hubo un error al registrar el usuario: " . $register_result['error']
                ]);
            }
        } else {
            // Error al crear la empresa
            echo json_encode($result);
        }
    } catch (Exception $e) {
        // Capturar cualquier excepción y devolver un error en JSON
        echo json_encode([
            'success' => false,
            'message' => "Error inesperado: " . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => "Método de solicitud no permitido."
    ]);
}
