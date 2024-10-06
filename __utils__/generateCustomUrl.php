<?php

require_once dirname(__DIR__) . '/configs/init.php';
require_once dirname(__DIR__) . '/classes/Database.php';
require_once dirname(__DIR__) . '/classes/CompanyManager.php';

$db  = new Database();

// Crear una instancia de la clase CompanyManager
$companyManager = new CompanyManager($db); // Asume que la clase tiene un constructor que acepta una conexión de base de datos

try {
    // Obtener todas las compañías de la base de datos
    $sql = "SELECT id, name FROM companies";
    $db->query($sql);
    $companies =  $db->resultSet();

    if ($companies) {
        foreach ($companies as $company) {
            $company_id = $company['id'];
            $company_name = $company['name'];

            // Aplicar el método urlConverter para cada compañía
            $companyManager->urlConverter($company_id, $company_name);

            echo "URL para la empresa {$company_name} generada con éxito.\n";
        }
    } else {
        echo "No se encontraron empresas en la base de datos.\n";
    }
} catch (Exception $e) {
    echo "Error al actualizar las URLs de las empresas: " . $e->getMessage() . "\n";
}
