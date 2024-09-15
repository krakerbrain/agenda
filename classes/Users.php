<?php
require_once 'Database.php';

class Users
{
    public function add_user($data)
    {
        $db = new Database();
        $db->query('INSERT INTO users (company_id, username, email, password, role_id) VALUES (:company_id, :username, :email, :password, :role_id)');
        $db->bind(':company_id', $data['company_id']);
        $db->bind(':username', $data['username']);
        $db->bind(':email', $data['email']);
        $db->bind(':password', $data['password']);
        $db->bind(':role_id', $data['role_id']);
        $db->execute();
        return $db->rowCount();
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
}
