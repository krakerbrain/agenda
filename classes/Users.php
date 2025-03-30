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
        $this->db->query('INSERT INTO users (company_id, name, email, password, role_id, token_sha256, created_at) 
                    VALUES (:company_id, :username, :email, :password, :role_id, :token, NOW())');
        $this->db->bind(':company_id', $data['company_id']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role_id', $data['role_id']);
        $this->db->bind(':token', $data['token']);
        $this->db->execute();
        return $this->db->rowCount();
    }

    public function register_user($data)
    {
        // Validaciones en el lado de PHP
        if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['password2']) || empty($data['company_id'])) {
            return ["success" => false, "error" => "Todos los campos son obligatorios"];
        }

        // Validar si las contraseñas coinciden
        if ($data['password'] !== $data['password2']) {
            return ["success" => false, "error" => "Las contraseñas no coinciden"];
        }

        // Llamar a la función de validación usando el procedimiento almacenado
        $validation_result = $this->validate_registration($data['username'], $data['email'], $data['password'], $data['password2']);

        if ($validation_result['error']) {
            return ["success" => false, "error" => $validation_result['error']];
        }

        // Si la validación pasa, generar hash y token
        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 7]);
        $token = hash('sha256', $data['username'] . $data['email']);

        // Llamar a la función para insertar al usuario
        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $hash,
            'company_id' => $data['company_id'],
            'role_id' => $data['role_id'],
            'token' => $token
        ];

        $result = $this->add_user($userData);

        if ($result) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => "Error al agregar el usuario a la base de datos"];
        }
    }

    // Función para validar usando el procedimiento almacenado
    public function validate_registration($username, $email, $password, $password2)
    {

        $this->db->query("CALL validar_registro(:usuario, :correo, :pass, :pass2, @error)");
        $this->db->bind(':usuario', $username);
        $this->db->bind(':correo', $email);
        $this->db->bind(':pass', $password);
        $this->db->bind(':pass2', $password2);
        $this->db->execute();

        // Obtener el mensaje de error desde la variable de sesión de MySQL
        $this->db->query("SELECT @error AS error");
        $error = $this->db->single();

        if (!empty($error['error'])) {
            return ["error" => $error['error']];
        }

        return ["error" => null];
    }
    public function getAboutRoles()
    {

        $this->db->query('SELECT id, type, about_role FROM user_role WHERE id > 2');
        return $this->db->resultSet();
    }
    public function get_users($company_id)
    {

        $this->db->query('SELECT u.id, u.name, u.email, ur.type as role_type FROM users u
                            JOIN user_role ur 
                            ON u.role_id = ur.id
                            WHERE u.company_id = :company
                            AND ur.id > 2 
                            ORDER BY u.role_id ASC');
        $this->db->bind(':company', $company_id);
        return $this->db->resultSet();
    }
    public function get_user($id)
    {

        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function get_user_for_login($correo)
    {
        // SELECT name, password, company_id, role_id, token_sha256 FROM users WHERE email = :usuario LIMIT 1

        $this->db->query('SELECT name, password, company_id, role_id, token_sha256 FROM users WHERE email = :usuario LIMIT 1');
        $this->db->bind(':usuario', $correo);
        return $this->db->single();
    }
    public function update_user($data)
    {

        $this->db->query('UPDATE users SET username = :username, email = :email, role_id = :role_id WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role_id', $data['role_id']);
        $this->db->execute();
        return $this->db->rowCount();
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
}
