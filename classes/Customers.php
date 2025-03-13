<?php
require_once dirname(__DIR__) . '/classes/Database.php';

class Customers
{
    private $db;


    public function __construct()
    {
        $this->db = new Database(); // Usa la clase Database

    }

    // crear metodo que verifique si el cliente ya existe verificando por el telefono y el correo, si existe retorna id
    public function checkAndAssociateCustomer($phone, $email, $companyId)
    {
        try {
            // Primero, verificamos si el cliente existe por teléfono o email
            $sql = "SELECT c.id
                    FROM customers c
                    LEFT JOIN company_customers cc ON c.id = cc.customer_id
                    WHERE (c.phone = :phone OR c.mail = :email)";
            $this->db->query($sql);
            $this->db->bind(':phone', $phone);
            $this->db->bind(':email', $email);
            $result = $this->db->single();

            // Si el cliente existe
            if ($result) {
                // Verificamos si el cliente ya está asociado a la compañía
                $customerId = $result['id'];
                $sqlCheckAssociation = "SELECT 1 FROM company_customers WHERE customer_id = :customer_id AND company_id = :company_id";
                $this->db->query($sqlCheckAssociation);
                $this->db->bind(':customer_id', $customerId);
                $this->db->bind(':company_id', $companyId);
                $associationExists = $this->db->single();

                // Si no está asociado, lo asociamos
                if (!$associationExists) {
                    $this->associate_company_with_customer($customerId, $companyId);
                }

                return $customerId;
            }

            // Si el cliente no existe, creamos uno nuevo
            return false;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }


    // crear metodo que agregue un cliente a la base de datos y retorne el id del cliente
    public function add_customer($data)
    {
        try {
            // Insertamos el cliente
            $sql = "INSERT INTO customers (name, phone, mail) VALUES (:name, :phone, :email)";
            $this->db->query($sql);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':email', $data['mail']);
            $this->db->execute();
            $customerId = $this->db->lastInsertId();

            // Asociamos la compañía con el cliente
            $this->associate_company_with_customer($customerId, $data['company_id']);

            return $customerId;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    private function associate_company_with_customer($customerId, $companyId)
    {
        try {
            $sql = "INSERT INTO company_customers (company_id, customer_id) VALUES (:company_id, :customer_id)";
            $this->db->query($sql);
            $this->db->bind(':company_id', $companyId);
            $this->db->bind(':customer_id', $customerId);
            $this->db->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
