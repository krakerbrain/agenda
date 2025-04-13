<?php
require_once 'Users.php';
require_once 'EmailTemplate.php';
require_once 'Schedules.php';

class UserRegistrationService
{
    private $users;
    private $emailTemplate;

    public function __construct()
    {
        $this->users = new Users();
        $this->emailTemplate = new EmailTemplate();
    }

    /**
     * Registra un nuevo usuario y configura su horario por defecto
     * 
     * @param array $userData Datos del usuario a registrar
     * @param int $company_id ID de la compañía
     * @return array Resultado de la operación
     */
    public function registerUser(array $userData, int $company_id): array
    {
        // Validar datos requeridos
        if (empty($userData['username'])) {
            return ['success' => false, 'error' => 'El nombre de usuario es requerido'];
        }
        if (empty($userData['email'])) {
            return ['success' => false, 'error' => 'El email es requerido'];
        }
        if (empty($userData['password'])) {
            return ['success' => false, 'error' => 'La contraseña es requerida'];
        }

        // Registrar el usuario
        $register_result = $this->users->register_user($userData);

        if (!$register_result['success']) {
            return $register_result;
        }

        $new_user_id = $register_result['user_id'];

        // Crear horario por defecto
        $user_schedule = new Schedules($company_id, $new_user_id);
        $schedule_result = $user_schedule->addNewSchedule();

        if (!$schedule_result) {
            return [
                'success' => false,
                'error' => 'Usuario registrado pero falló la creación del horario'
            ];
        }

        // Enviar email de activación
        $emailSent = $this->emailTemplate->buildInscriptionAlert($userData['email']);

        if (!$emailSent) {
            return [
                'success' => true,
                'warning' => 'Usuario registrado pero falló el envío del email'
            ];
        }

        return [
            'success' => true,
            'message' => 'Usuario registrado con éxito. Se ha enviado un correo para activar la cuenta.',
            'user_id' => $new_user_id
        ];
    }
}
