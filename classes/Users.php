<?php
require_once 'Database.php';

class Users
{
    public function add_user($data)
    {
        $db = new Database();
        $db->query('INSERT INTO users (company_id, name, email, password, role_id, token_sha256, created_at) 
                    VALUES (:company_id, :username, :email, :password, :role_id, :token, NOW())');
        $db->bind(':company_id', $data['company_id']);
        $db->bind(':username', $data['username']);
        $db->bind(':email', $data['email']);
        $db->bind(':password', $data['password']);
        $db->bind(':role_id', $data['role_id']);
        $db->bind(':token', $data['token']);
        $db->execute();
        return $db->rowCount();
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
        $db = new Database();
        $db->query("CALL validar_registro(:usuario, :correo, :pass, :pass2, @error)");
        $db->bind(':usuario', $username);
        $db->bind(':correo', $email);
        $db->bind(':pass', $password);
        $db->bind(':pass2', $password2);
        $db->execute();

        // Obtener el mensaje de error desde la variable de sesión de MySQL
        $db->query("SELECT @error AS error");
        $error = $db->single();

        if (!empty($error['error'])) {
            return ["error" => $error['error']];
        }

        return ["error" => null];
    }
    public function get_roles()
    {
        $db = new Database();
        $db->query('SELECT * FROM roles');
        return $db->resultSet();
    }
    public function get_users($company_id)
    {
        $db = new Database();
        $db->query('SELECT u.id, u.name, u.email, ur.type as role_type FROM users u
                            JOIN user_role ur 
                            ON u.role_id = ur.id
                            WHERE u.company_id = :company
                            AND ur.id > 2 
                            ORDER BY u.id DESC');
        $db->bind(':company', $company_id);
        return $db->resultSet();
    }
    public function get_user($id)
    {
        $db = new Database();
        $db->query('SELECT * FROM users WHERE id = :id');
        $db->bind(':id', $id);
        return $db->single();
    }

    public function get_user_for_login($correo)
    {
        // SELECT name, password, company_id, role_id, token_sha256 FROM users WHERE email = :usuario LIMIT 1
        $db = new Database();
        $db->query('SELECT name, password, company_id, role_id, token_sha256 FROM users WHERE email = :usuario LIMIT 1');
        $db->bind(':usuario', $correo);
        return $db->single();
    }
    public function update_user($data)
    {
        $db = new Database();
        $db->query('UPDATE users SET username = :username, email = :email, role_id = :role_id WHERE id = :id');
        $db->bind(':id', $data['id']);
        $db->bind(':username', $data['username']);
        $db->bind(':email', $data['email']);
        $db->bind(':role_id', $data['role_id']);
        $db->execute();
        return $db->rowCount();
    }
    public function delete_user($id)
    {
        $db = new Database();
        $db->query('DELETE FROM users WHERE id = :id');
        $db->bind(':id', $id);
        $db->execute();
        return $db->rowCount();
    }
    public function delete_user_by_company($company_id)
    {
        $db = new Database();
        $db->query('DELETE FROM users WHERE company_id = :company_id');
        $db->bind(':company_id', $company_id);
        $db->execute();
        return ['success' => true];
    }
}