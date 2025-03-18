<?php
require_once dirname(__DIR__) . '/classes/Database.php';

class Customers
{
    private $db;


    public function __construct()
    {
        $this->db = new Database(); // Usa la clase Database

    }

    public function get_paginated_customers($company_id, $status, $offset, $limit)
    {
        $db = new Database();

        // Consulta base
        $query = 'SELECT c.*, 
                     CASE WHEN ci.id IS NOT NULL THEN 1 ELSE 0 END AS has_incidents
              FROM customers c
              LEFT JOIN company_customers cc ON c.id = cc.customer_id
              LEFT JOIN customer_incidents ci ON c.id = ci.customer_id
              WHERE cc.company_id = :company';

        // Filtros según el estado
        if ($status === 'incidencias') {
            $query .= ' AND ci.id IS NOT NULL';  // Solo clientes con incidencias
        } elseif ($status === 'blocked') {
            $query .= ' AND c.blocked = 1';  // Solo clientes bloqueados
        }

        // Agrupar por cliente para evitar duplicados
        $query .= ' GROUP BY c.id';

        // Paginación
        $query .= ' LIMIT :offset, :limit';

        // Preparar y ejecutar la consulta
        $db->query($query);
        $db->bind(':company', $company_id);
        $db->bind(':offset', $offset, PDO::PARAM_INT);
        $db->bind(':limit', $limit, PDO::PARAM_INT);

        return $db->resultSet();
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
    // Método para crear una incidencia
    public function createIncident($customerId, $reason, $notes)
    {
        try {
            $query = 'INSERT INTO customer_incidents (customer_id, description, incident_date, note)
                  VALUES (:customerId, :reason, NOW(), :notes)';

            $this->db->query($query);
            $this->db->bind(':customerId', $customerId);
            $this->db->bind(':reason', $reason);
            $this->db->bind(':notes', $notes);

            return $this->db->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Método para obtener los detalles de un cliente
    public function getCustomerDetail($customerId, $companyId)
    {
        try {
            // Consulta para obtener los detalles básicos del cliente
            $query = 'SELECT c.id, c.name, c.phone, c.mail, c.blocked, 
                             CASE WHEN ci.id IS NOT NULL THEN 1 ELSE 0 END AS has_incidents
                      FROM customers c
                      LEFT JOIN customer_incidents ci ON c.id = ci.customer_id
                      LEFT JOIN company_customers cc ON c.id = cc.customer_id
                      WHERE c.id = :customerId
                      AND cc.company_id = :company
                      GROUP BY c.id';

            $this->db->query($query);
            $this->db->bind(':customerId', $customerId);
            $this->db->bind(':company', $companyId);
            $customerDetails = $this->db->single();

            if (!$customerDetails) {
                return null; // Si no se encuentra el cliente, retornar null
            }

            // Consulta para obtener los últimos servicios del cliente
            $servicesQuery = 'SELECT 
                s.id AS service_id, 
                s.name AS service_name, 
                a.date AS appointment_date, 
                a.start_time, 
                a.end_time, 
                a.status AS appointment_status
            FROM 
                appointments a
            INNER JOIN 
                services s ON a.id_service = s.id
            WHERE 
                a.customer_id = :customerId
                AND s.company_id = :company
            ORDER BY 
                a.date DESC, 
                a.start_time DESC
            LIMIT 5'; // Limitar a los últimos 5 servicios

            $this->db->query($servicesQuery);
            $this->db->bind(':customerId', $customerId);
            $this->db->bind(':company', $companyId);
            $lastServices = $this->db->resultSet();

            // Consulta para obtener las incidencias del cliente
            $incidentsQuery = 'SELECT ci.id, ci.description, ci.incident_date, ci.note
                               FROM customer_incidents ci
                               LEFT JOIN customers c ON ci.customer_id = c.id
                               LEFT JOIN company_customers cc ON c.id = cc.customer_id
                               WHERE ci.customer_id = :customerId
                                 AND cc.company_id = :company
                               ORDER BY ci.incident_date DESC';

            $this->db->query($incidentsQuery);
            $this->db->bind(':customerId', $customerId);
            $this->db->bind(':company', $companyId);
            $incidents = $this->db->resultSet();

            // Combinar toda la información en un solo array
            $customerDetails['last_services'] = $lastServices;
            $customerDetails['incidents'] = $incidents;

            return $customerDetails;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    public function getCustomerById($customerId)
    {
        try {
            $query = 'SELECT * FROM customers WHERE id = :customerId';
            $this->db->query($query);
            $this->db->bind(':customerId', $customerId);
            return $this->db->single();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
}
