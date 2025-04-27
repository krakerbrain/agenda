<?php
require_once 'Database.php';

class Users
{
    private $db;

    public function __construct(Database $db = null)
    {
        $this->db = $db ?: new Database();
    }
    public function add_user($data)
    {
        try {
            $this->db->query('INSERT INTO users (company_id, name, email, password, role_id, token_sha256, created_at, url_pic, description) 
                        VALUES (:company_id, :username, :email, :password, :role_id, :token, NOW(), :url_pic, :description)');

            $this->db->bind(':company_id', $data['company_id']);
            $this->db->bind(':username', $data['username']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':password', $data['password']);
            $this->db->bind(':role_id', $data['role_id']);
            $this->db->bind(':token', $data['token']);
            $this->db->bind(':url_pic', $data['url_pic']);
            $this->db->bind(':description', $data['description']);

            if ($this->db->execute()) {
                return [
                    'success' => true,
                    'user_id' => $this->db->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Error al ejecutar la inserción'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error en add_user(): " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error en la base de datos'
            ];
        }
    }


    public function register_user($data)
    {
        try {
            // Validaciones básicas
            if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['password2']) || empty($data['company_id'])) {
                return ["success" => false, "error" => "Todos los campos son obligatorios"];
            }

            if ($data['password'] !== $data['password2']) {
                return ["success" => false, "error" => "Las contraseñas no coinciden"];
            }

            // Validación adicional (procedimiento almacenado)
            $validation_result = $this->validate_registration($data['username'], $data['email'], $data['password'], $data['password2']);
            if ($validation_result['error']) {
                return $validation_result;
            }

            // Preparar datos
            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 7]),
                'company_id' => $data['company_id'],
                'role_id' => $data['role_id'],
                'token' => hash('sha256', $data['username'] . $data['email']),
                'url_pic' => $data['url_pic'] ?? null,
                'description' => $data['description'] ?? null
            ];

            // Insertar y retornar resultado completo
            return $this->add_user($userData);
        } catch (Exception $e) {
            return ["success" => false, "error" => "Error en el registro: " . $e->getMessage()];
        }
    }

    // Función para validar usando el procedimiento almacenado
    public function validate_registration($username, $email, $password, $password2)
    {
        try {
            $this->db->query("CALL validar_registro(:usuario, :correo, :pass, :pass2, @error)");
            $this->db->bind(':usuario', $username);
            $this->db->bind(':correo', $email);
            $this->db->bind(':pass', $password);
            $this->db->bind(':pass2', $password2);
            $this->db->execute();

            // Obtener el mensaje de error desde la variable de sesión de MySQL
            $this->db->query("SELECT @error AS error");
            $error = $this->db->single();

            return [
                'success' => empty($error['error']),
                'error' => $error['error'] ?? null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => "Error en validación: " . $e->getMessage()
            ];
        }
    }
    public function get_user_role($user_id)
    {
        $this->db->query('SELECT role_id FROM users WHERE id = :user_id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->singleValue(); // Devuelve directamente el role_id o null
    }
    public function getAboutRoles()
    {

        $this->db->query('SELECT id, type, about_role FROM user_role WHERE id >= 2');
        return $this->db->resultSet();
    }
    public function get_users($company_id)
    {

        $this->db->query('SELECT u.id, u.name, u.email, u.url_pic, u.description, ur.type as role_type FROM users u
                            JOIN user_role ur 
                            ON u.role_id = ur.id
                            WHERE u.company_id = :company
                            ORDER BY u.role_id ASC');
        $this->db->bind(':company', $company_id);
        return $this->db->resultSet();
    }

    public function get_all_users($company_id)
    {

        $this->db->query('SELECT u.id, u.name, u.role_id FROM users u
                            WHERE u.company_id = :company
                            ORDER BY u.role_id ASC');
        $this->db->bind(':company', $company_id);
        return $this->db->resultSet();
    }
    public function getUserForEdit($user_id, $company_id)
    {
        $this->db->query('SELECT u.id, u.name, u.email, u.url_pic, u.description, u.role_id  
                            FROM users u
                            WHERE u.company_id = :company_id AND u.id = :user_id');
        $this->db->bind(':company_id', $company_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }


    public function get_user_for_login($correo)
    {
        // SELECT name, password, company_id, role_id, token_sha256 FROM users WHERE email = :usuario LIMIT 1

        $this->db->query('SELECT id as user_id, name, password, company_id, role_id, token_sha256 FROM users WHERE email = :usuario LIMIT 1');
        $this->db->bind(':usuario', $correo);
        return $this->db->single();
    }
    public function update_user($data)
    {
        // Consulta base
        $query = 'UPDATE users SET 
        name = :name, 
        email = :email, 
                  role_id = :role_id, 
                  description = :description, 
                  updated_at = NOW(),
                  token_sha256 = :token';

        // Agregar foto si está presente
        if (!empty($data['url_pic'])) {
            $query .= ', url_pic = :url_pic';
        }

        $query .= ' WHERE id = :id AND company_id = :company_id';

        $this->db->query($query);
        // Bind de parámetros obligatorios
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':company_id', $data['company_id']);
        $this->db->bind(':name', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role_id', $data['role_id']);
        $this->db->bind(':description', $data['description'] ?? null);

        $token = hash('sha256', $data['username'] . $data['email']);
        $this->db->bind(':token', $token);

        // Bind opcional de la foto
        if (!empty($data['url_pic'])) {
            $this->db->bind(':url_pic', $data['url_pic']);
        }

        $this->db->execute();

        // Verificar si se actualizó algún registro
        if ($this->db->rowCount() === 0) {
            throw new Exception('No se actualizó el usuario o no tienes permisos');
        }

        return true;
    }
    public function delete_user($id)
    {

        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();
        return $this->db->rowCount();
    }
    public function delete_user_by_company($company_id)
    {

        $this->db->query('DELETE FROM users WHERE company_id = :company_id');
        $this->db->bind(':company_id', $company_id);
        $this->db->execute();
        return ['success' => true];
    }

    // Recovery password functions

    // Dentro de tu clase Users (que debería usar Database)
    public function get_user_by_email($email)
    {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function save_password_reset_token($userId, $token, $expires)
    {
        $this->db->query("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':token', $token);
        $this->db->bind(':expires_at', $expires);
        return $this->db->execute();
    }

    public function validate_reset_token($token)
    {
        $this->db->query("SELECT * FROM password_resets WHERE token = :token AND used = 0");
        $this->db->bind(':token', $token);
        return $this->db->single();
    }

    public function update_user_password($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = :password WHERE id = :id");
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    public function updateUrlPic($userId, $url_pic)
    {
        $this->db->query("UPDATE users SET url_pic = :url_pic WHERE id = :id");
        $this->db->bind(':url_pic', $url_pic);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    public function invalidate_reset_token($token)
    {
        $this->db->query("UPDATE password_resets SET used = 1 WHERE token = :token");
        $this->db->bind(':token', $token);
        return $this->db->execute();
    }

    // Método adicional para verificar si el token pertenece al usuario
    public function verify_user_reset_token($userId, $token)
    {
        $this->db->query("SELECT * FROM password_resets WHERE user_id = :user_id AND token = :token AND used = 0 AND expires_at > NOW()");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':token', $token);
        return $this->db->single();
    }

    public function count_company_users($company_id)
    {
        $this->db->query('SELECT COUNT(*) FROM users WHERE company_id = :company_id');
        $this->db->bind(':company_id', $company_id);
        return (int)$this->db->singleValue(); // Convertimos a entero el conteo
    }

    // para get_service_providers.php
    public function getProvidersByService($serviceId, $companyId)
    {
        $sql = "SELECT u.id, u.name, u.email, u.url_pic, u.description,
                     us.available_days as provider_days, 
                     us.is_active as provider_active
              FROM users u
              JOIN user_services us ON u.id = us.user_id
              WHERE us.service_id = :service_id
              AND u.company_id = :company_id
              ORDER BY u.name";

        $this->db->query($sql);
        $this->db->bind(':service_id', $serviceId);
        $this->db->bind(':company_id', $companyId);

        return $this->db->resultSet();
    }
}
