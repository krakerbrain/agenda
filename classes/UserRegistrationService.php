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
    public function registerUser(array $userData, int $company_id, bool $sendEmail): array
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
        if ($sendEmail) {
            $emailSent = $this->emailTemplate->buildInscriptionAlert($userData['email']);

            if (!$emailSent) {
                return [
                    'success' => true,
                    'warning' => 'Usuario registrado pero falló el envío del email'
                ];
            }
        }

        return [
            'success' => true,
            'message' => 'Usuario registrado con éxito',
            'user_id' => $new_user_id
        ];
    }

    public function registerInitialUserFromWeb(string $owner_name, string $email, int $company_id): array
    {
        try {
            $temporal_pass = password_hash('temporal123', PASSWORD_BCRYPT, ['cost' => 7]);

            $data = [
                'username' => $owner_name,
                'email' => $email,
                'password' => $temporal_pass,
                'password2' => $temporal_pass,
                'company_id' => $company_id,
                'role_id' => 2, // Asignar como administrador
                'token' => hash('sha256', $owner_name . $email),
            ];

            $result = $this->users->registerInitialUserFromInscription($data);

            if (!$result['success']) {
                return $result;
            }

            // Crear horario por defecto
            $user_schedule = new Schedules($company_id, $result['user_id']);
            $schedule_result = $user_schedule->addNewSchedule();

            if (!$schedule_result) {
                return [
                    'success' => false,
                    'error' => 'Usuario creado pero falló la asignación del horario'
                ];
            }

            // Enviar email de activación

            $emailSent = $this->emailTemplate->buildInscriptionAlert($data['email']);

            if (!$emailSent) {
                return [
                    'success' => true,
                    'warning' => 'Usuario registrado pero falló el envío del email'
                ];
            }


            return [
                'success' => true,
                'user_id' => $result['user_id']
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error al registrar el usuario principal: ' . $e->getMessage()];
        }
    }

    public function emailExists(string $email): bool
    {
        return $this->users->emailExists($email);
    }


    public function updateUrlPic($user_id, $url_pic)
    {
        return $this->users->updateUrlPic($user_id, $url_pic);
    }
}
