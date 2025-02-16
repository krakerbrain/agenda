<?php

require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/Database.php';
require_once dirname(__DIR__) . '/classes/FileManager.php';
require_once dirname(__DIR__) . '/classes/IntegrationManager.php';
require_once dirname(__DIR__) . '/classes/Logger.php';

class CompanyManager
{
    private $db;
    private $fileManager;
    private $integrationManager;
    private $logger;


    // Modificar el constructor para aceptar dependencias
    public function __construct(Database $db = null, FileManager $fileManager = null, IntegrationManager $integrationManager = null)
    {
        $this->db = $db ?? new Database(); // Usa la clase Database o la inyectada
        $this->fileManager = $fileManager ?? new FileManager(); // Usa FileManager o la inyectada
        $this->integrationManager = $integrationManager ?? new IntegrationManager(); // Usa FileManager o la inyectada
        $this->logger = new Logger(); // Usa Logger
    }

    public function getAllActiveCompanies()
    {
        $sql = "SELECT id, fixed_start_date, auto_open, fixed_duration FROM companies WHERE is_active = 1";
        $this->db->query($sql);
        return $this->db->resultSet();
    }


    public function getCompanyDataForCompanyList()
    {
        $sql = "SELECT id, name, logo, is_active, custom_url FROM companies";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getCompanyDataForDatosEmpresa($company_id)
    {
        $sql = "SELECT name, logo, selected_banner, phone, address, description FROM companies WHERE id = :id AND is_active = 1";
        $this->db->query($sql);
        $this->db->bind(':id', $company_id);
        return $this->db->resultSet();
    }
    public function getAllCompanyData($company_id)
    {
        $sql = "SELECT * FROM companies WHERE id = :id AND is_active = 1";
        $this->db->query($sql);
        $this->db->bind(':id', $company_id);
        return $this->db->single();
    }

    /**
     * Verifica si una empresa existe y está activa.
     * Usado en lógica de validación antes de obtener horarios o datos de la empresa.
     */
    public function getCompanyTimeStep($company_id)
    {
        $sql = "SELECT time_step FROM companies WHERE id = :id AND is_active = 1";
        $this->db->query($sql);
        $this->db->bind(':id', $company_id);
        return $this->db->single(); // Retorna un array asociativo con 'time_step' o null si no existe
    }

    public function getCompanyCustomUrl($company_id)
    {
        $sql = "SELECT custom_url FROM companies WHERE id = :id AND is_active = 1";
        $this->db->query($sql);
        $this->db->bind(':id', $company_id);
        return $this->db->singleValue();
    }

    public function getFixedStartDate($companyId)
    {

        $query = "SELECT fixed_start_date FROM companies WHERE id = :company_id";
        $this->db->query($query);
        $this->db->bind(':company_id', $companyId);
        $result = $this->db->single();

        // Devolver la fecha si existe, de lo contrario, falso
        return $result ? $result['fixed_start_date'] : false;
    }
    // Función para crear una nueva empresa
    public function createCompany($name, $phone, $address, $logo = null, $status = 0)
    {
        /**
         * // este token cumplia la funcion de usarlo al final de la url para dirigir a la pagina de reservas. Sin embargo se cambio por una url amigable por lo cual ya no es necesario a menos que se le de otro uso, por ahora no cumple ninguna funcion
         */
        $token = bin2hex(random_bytes(16));

        try {
            // Formatear el número de teléfono
            $this->db->beginTransaction(); // Iniciar transacción

            $phone = $this->formatPhoneNumber($phone);
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

            $this->urlConverter($company_id, $name);

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

            // Aquí no detenemos la transacción, solo registramos los errores si ocurren


            $this->db->endTransaction(); // Commit de la transacción

            $integrationResult = $this->integrationManager->createDefaultIntegrationsForCompany($company_id);

            // Verificar si hubo un error al crear las integraciones
            if (!$integrationResult['success']) {
                $this->logger->logError('Error al crear integraciones para la empresa con ID ' . $company_id . ': ' . $integrationResult['error']);
            }

            return ['success' => true, 'company_id' => $company_id];
        } catch (Exception $e) {
            $this->db->cancelTransaction(); // Rollback de la transacción

            // Manejo de excepciones para el número de teléfono inválido
            if ($e->getCode() === 1001) {
                return [
                    'success' => false,
                    'error' => 'Número de teléfono inválido.', // Mensaje específico
                    'debug' => $e->getMessage()
                ];
            }

            // Verificar si el error es de tipo "clave duplicada"
            if ($e->getCode() == 23000 && strpos($e->getMessage(), '1062 Duplicate entry') !== false) {
                return [
                    'success' => false,
                    'error' => 'Esta empresa ya ha sido creada.',
                    'debug' => $e->getMessage() // Añadimos el mensaje técnico para el log
                ];
            }

            // Otro tipo de error
            return [
                'success' => false,
                'error' => 'Error al agregar la empresa.',
                'debug' => $e->getMessage() // Añadimos el mensaje técnico para el log
            ];
        }
    }

    private function formatPhoneNumber($telefono)
    {
        // Eliminar espacios en blanco, guiones y paréntesis, pero mantener el símbolo "+"
        $telefono = preg_replace('/[\s\-\(\)]/', '', $telefono);

        // Si el número empieza con "+56" y tiene 11 dígitos, es correcto
        if (preg_match('/^\+56\d{9}$/', $telefono)) {
            return $telefono;
        }

        // Si el número ya empieza con "56" y tiene 11 dígitos, añadir "+"
        if (preg_match('/^56\d{9}$/', $telefono)) {
            return '+' . $telefono;
        }

        // Si el número tiene 8 dígitos y empieza con "9" (móvil chileno), agregar "+56"
        if (preg_match('/^9\d{8}$/', $telefono)) {
            return '+56' . $telefono;
        }

        // Si el número tiene 8 dígitos (número fijo chileno), agregar "+569"
        if (preg_match('/^\d{8}$/', $telefono)) {
            return '+569' . $telefono;
        }

        // Si el número no es válido, lanzar una excepción
        throw new Exception('Número de teléfono inválido.', 1001);
    }

    public function urlConverter($company_id, $company_name)
    {
        // Limpiar el nombre de la empresa
        $cleanedCompanyName = $this->cleanCompanyName($company_name);
        $max_length = 20;

        // Generar el slug base
        $baseSlug = URLify::slug($cleanedCompanyName, $max_length, '-');
        $count = 0;

        // Buscar una URL única
        do {
            $urlSlug = $baseSlug . ($count > 0 ? '-' . $count : '');
            $sql = "SELECT COUNT(*) AS count FROM companies WHERE custom_url = :url";
            $this->db->query($sql);
            $this->db->bind(':url', $urlSlug);
            $result = $this->db->single();

            if (isset($result['count']) && $result['count'] == 0) {
                // URL no existe, romper el bucle
                break;
            }

            $count++;
        } while (true);  // Terminará cuando encuentre un slug único

        // Guardar la URL en la base de datos
        $query = "UPDATE companies SET custom_url = :url WHERE id = :id";
        $this->db->query($query);
        $this->db->bind(':url', $urlSlug);
        $this->db->bind(':id', $company_id);
        $this->db->execute();

        return true;
    }

    private function cleanCompanyName($companyName)
    {
        // Definir palabras a eliminar
        $removeWords = ['la', 'los', 'el', 'y', 'de'];

        // Convertir el nombre a minúsculas
        $companyName = mb_strtolower($companyName);

        // Eliminar palabras no deseadas
        $companyName = preg_replace('/\b(' . implode('|', $removeWords) . ')\b/', '', $companyName);

        // Eliminar caracteres especiales y reemplazar múltiples espacios con uno solo
        $companyName = preg_replace('/[^a-z0-9\s-]/', '', $companyName);
        $companyName = preg_replace('/\s+/', ' ', $companyName);

        // Trim spaces
        $companyName = trim($companyName);

        // Reemplazar espacios con guiones
        $companyName = str_replace(' ', '-', $companyName);

        return $companyName;
    }


    // Función para actualizar los datos de una empresa
    public function updateCompanyData($company_id, $data)
    {
        try {
            $this->db->beginTransaction(); // Iniciar transacción

            // Asignar valores de $data
            $phone = $data['phone'] ?? null;
            $address = $data['address'] ?? null;
            $description = $data['description'] ?? null;
            $logo = $data['logo'] ?? null;
            $selected_banner = $data['selected_banner'] ?? null;

            // Actualizar los datos de la empresa
            $sql = "UPDATE companies SET logo = :logo, phone = :phone, address = :address, description = :description, selected_banner = :selected_banner WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':phone', $phone);
            $this->db->bind(':address', $address);
            $this->db->bind(':description', $description);
            $this->db->bind(':logo', $logo);
            $this->db->bind(':id', $company_id);
            $this->db->bind(':selected_banner', $selected_banner);
            $this->db->execute();

            $this->db->endTransaction(); // Commit de la transacción

            return ['success' => true, 'message' => 'Datos de la empresa actualizados correctamente'];
        } catch (Exception $e) {
            $this->db->cancelTransaction(); // Rollback de la transacción en caso de error
            return ['success' => false, 'error' => 'Error al actualizar la empresa: ' . $e->getMessage()];
        }
    }

    public function updateCompanyConfig($data)
    {
        try {
            $this->db->beginTransaction(); // Iniciar transacción

            // Preparar la consulta SQL
            $sql = "UPDATE companies SET 
                        schedule_mode = :schedule_mode,
                        calendar_mode = :calendar_mode,
                        bg_color = :bg_color,
                        font_color = :font_color,
                        btn1 = :btn1_color,
                        btn2 = :btn2_color,
                        calendar_days_available = :calendar_days_available,
                        fixed_start_date = :fixed_start_date,
                        fixed_duration = :fixed_duration,
                        auto_open = :auto_open,
                        time_step = :time_step
                    WHERE id = :company_id AND is_active = 1";

            $this->db->query($sql);
            $this->db->bind(':schedule_mode', $data['schedule_mode']);
            $this->db->bind(':calendar_mode', $data['calendar_mode']);
            $this->db->bind(':bg_color', $data['bg_color']);
            $this->db->bind(':font_color', $data['font_color']);
            $this->db->bind(':btn1_color', $data['btn1_color']);
            $this->db->bind(':btn2_color', $data['btn2_color']);
            $this->db->bind(':calendar_days_available', $data['calendar_days_available']);
            $this->db->bind(':fixed_start_date', $data['fixed_start_date']);
            $this->db->bind(':fixed_duration', $data['fixed_duration']);
            $this->db->bind(':auto_open', $data['auto_open']);
            $this->db->bind(':company_id', $data['company_id']);
            $this->db->bind(':time_step', $data['time_step']);
            $this->db->execute();

            $this->db->endTransaction(); // Commit de la transacción

            return ['success' => true];
        } catch (Exception $e) {
            $this->db->cancelTransaction(); // Rollback en caso de error
            return ['success' => false, 'error' => 'Error al actualizar los datos: ' . $e->getMessage()];
        }
    }

    public function updateFixedStartDay($companyId, $newStartDay)
    {
        try {
            // Asegurarse de que la nueva fecha sea un objeto DateTime
            if (!$newStartDay instanceof DateTime) {
                $newStartDay = new DateTime($newStartDay);
            }

            // Formatear la fecha a 'Y-m-d' para la consulta SQL
            $formattedDate = $newStartDay->format('Y-m-d');

            $this->db->beginTransaction(); // Iniciar transacción

            // Crear la consulta para actualizar el fixed_start_date
            $query = "UPDATE companies SET fixed_start_date = :fixed_start_date WHERE id = :company_id";

            // Preparar la consulta
            $this->db->query($query);
            $this->db->bind(':fixed_start_date', $formattedDate);
            $this->db->bind(':company_id', $companyId);
            $this->db->execute();

            $this->db->endTransaction(); // Commit de la transacción

            // Registrar el éxito en el log
            error_log("Se actualizó fixed_start_date para la compañía ID: $companyId a $formattedDate" . PHP_EOL, 3, dirname(__DIR__) . '/cron/cronerror/error.log');
            return true; // Retornar verdadero si la actualización fue exitosa
        } catch (Exception $e) {
            $this->db->cancelTransaction(); // Rollback en caso de error
            // Registrar el error si la actualización falla
            error_log("Error al actualizar fixed_start_date para la compañía ID: $companyId - " . $e->getMessage() . PHP_EOL, 3, dirname(__DIR__) . '/cron/cronerror/error.log');
            return false; // Retornar falso si la actualización falló
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

    public function removePastBlockedDates($company_id)
    {
        // Obtener las fechas bloqueadas
        $sql = "SELECT blocked_dates FROM companies WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $company_id);
        $blockedDates = $this->db->single()['blocked_dates'];

        // Convertir las fechas bloqueadas a un array
        $datesArray = explode(',', $blockedDates);
        $currentDate = date('Y-m-d');

        // Filtrar las fechas que ya han pasado
        $futureDates = array_filter($datesArray, function ($date) use ($currentDate) {
            return $date >= $currentDate;
        });

        // Volver a convertir el array en una cadena de texto
        $updatedBlockedDates = implode(',', $futureDates);

        // Actualizar la base de datos
        $sqlUpdate = "UPDATE companies SET blocked_dates = :blocked_dates WHERE id = :id";
        $this->db->query($sqlUpdate);
        $this->db->bind(':blocked_dates', $updatedBlockedDates);
        $this->db->bind(':id', $company_id);
        return $this->db->execute();
    }
}
