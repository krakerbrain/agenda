<?php
require_once dirname(__DIR__) . '/classes/Database.php';
require_once dirname(__DIR__) . '/classes/FileManager.php';

class CompanyManager
{
    private $db;
    private $fileManager;

    public function __construct()
    {
        $this->db = new Database(); // Usa la clase Database
        $this->fileManager = new FileManager();
    }

    public function getCompanyDataForCompanyList()
    {
        $sql = "SELECT id, name, logo, is_active, token FROM companies";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getCompanyDataForDatosEmpresa($company_id)
    {
        $sql = "SELECT name, logo, phone, address, description FROM companies WHERE id = :id AND is_active = 1";
        $this->db->query($sql);
        $this->db->bind(':id', $company_id);
        return $this->db->resultSet();
    }
    // Función para crear una nueva empresa
    public function createCompany($name, $phone, $address, $logo = null, $status = 1)
    {
        // Generar un token aleatorio para la empresa
        $token = bin2hex(random_bytes(16));

        try {
            $this->db->beginTransaction(); // Iniciar transacción

            // Subir el logo si existe
            $logoName = null;
            if ($logo && $logo['error'] === UPLOAD_ERR_OK) {
                $logoName = $logo['name'];
            }

            // Inserta la empresa en la base de datos
            $sql = "INSERT INTO companies (name, logo, phone, address, is_active, token) 
                    VALUES (:name, :logo, :phone, :address, :status, :token)";
            $this->db->query($sql);
            $this->db->bind(':name', $name);
            $this->db->bind(':logo', $logoName);
            $this->db->bind(':phone', $phone);
            $this->db->bind(':address', $address);
            $this->db->bind(':status', $status);
            $this->db->bind(':token', $token);
            $this->db->execute();

            // Obtener el ID de la compañía recién creada
            $company_id = $this->db->lastInsertId();

            // Subir el logo y actualizar el registro si se subió
            if ($logo && $logo['error'] === UPLOAD_ERR_OK) {
                $logoName = $this->fileManager->uploadLogo($name, $company_id);
                $this->db->query("UPDATE companies SET logo = :logo WHERE id = :company_id");
                $this->db->bind(':logo', $logoName);
                $this->db->bind(':company_id', $company_id);
                $this->db->execute();
            }

            // Insertar los horarios de trabajo de la nueva compañía
            $days = [1, 2, 3, 4, 5, 6, 7]; // Lunes a Domingo
            foreach ($days as $day) {
                $sql = "INSERT INTO company_schedules 
                        (company_id, day_id, work_start, work_end, break_start, break_end, is_enabled) 
                        VALUES (:company_id, :day_id, NULL, NULL, NULL, NULL, 1)";
                $this->db->query($sql);
                $this->db->bind(':company_id', $company_id);
                $this->db->bind(':day_id', $day);
                $this->db->execute();
            }

            $this->db->endTransaction(); // Commit de la transacción

            return ['success' => true, 'company_id' => $company_id];
        } catch (Exception $e) {
            $this->db->cancelTransaction(); // Rollback de la transacción en caso de error
            return ['success' => false, 'error' => 'Error al agregar la empresa: ' . $e->getMessage()];
        }
    }

    // Función para actualizar los datos de una empresa
    // $sql = $conn->prepare("UPDATE companies SET logo = :logo, phone = :phone, address = :address, description = :description WHERE id = :id");
    public function updateCompanyData($company_id, $data)
    {
        try {
            $this->db->beginTransaction(); // Iniciar transacción

            // Asignar valores de $data
            $phone = $data['phone'] ?? null;
            $address = $data['address'] ?? null;
            $description = $data['description'] ?? null;
            $logo = $data['logo'] ?? null;

            // Actualizar los datos de la empresa
            $sql = "UPDATE companies SET logo = :logo, phone = :phone, address = :address, description = :description WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':phone', $phone);
            $this->db->bind(':address', $address);
            $this->db->bind(':description', $description);
            $this->db->bind(':logo', $logo);
            $this->db->bind(':id', $company_id);
            $this->db->execute();

            $this->db->endTransaction(); // Commit de la transacción

            return ['success' => true, 'message' => 'Datos de la empresa actualizados correctamente'];
        } catch (Exception $e) {
            $this->db->cancelTransaction(); // Rollback de la transacción en caso de error
            return ['success' => false, 'error' => 'Error al actualizar la empresa: ' . $e->getMessage()];
        }
    }


    // Función para cambiar estado de empresa
    public function updateCompanyStatus($data)
    {
        try {
            // Actualizar la empresa
            $sql = "UPDATE companies SET is_active = :status WHERE id = :company_id";
            $this->db->query($sql);
            $this->db->bind(':status', $data['is_active']);
            $this->db->bind(':company_id', $data['id']);
            $this->db->execute();

            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Error al actualizar la empresa: ' . $e->getMessage()];
        }
    }

    public function deleteCompany($company_id)
    {
        try {
            // Eliminar la empresa
            $sql = "DELETE FROM companies WHERE id = :company_id";
            $this->db->query($sql);
            $this->db->bind(':company_id', $company_id);
            $this->db->execute();

            return ['success' => true];
        } catch (Exception $e) {

            return ['success' => false, 'error' => 'Error al eliminar la empresa: ' . $e->getMessage()];
        }
    }
}
