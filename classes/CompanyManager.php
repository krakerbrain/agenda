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

    // Función para crear una nueva empresa
    public function createCompany($name, $phone, $address, $logo = null)
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
                    VALUES (:name, :logo, :phone, :address, 1, :token)";
            $this->db->query($sql);
            $this->db->bind(':name', $name);
            $this->db->bind(':logo', $logoName);
            $this->db->bind(':phone', $phone);
            $this->db->bind(':address', $address);
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
}