<?php
require_once 'configs/init.php';
// Conexión a la base de datos
require_once 'classes/Database.php'; // Ajusta según tu estructura

// Directorio base donde se almacenan los logos
$baseDir = __DIR__ . '/assets/img/uploads/';

// 1. Obtener todas las empresas de la base de datos
try {
    $db = new Database();
    $db->query("SELECT id, name as nombre, logo FROM companies");
    $empresas = $db->resultset();
    if (empty($empresas)) {
        throw new Exception("No se encontraron empresas en la base de datos.");
    }
} catch (PDOException $e) {
    die("Error al obtener empresas: " . $e->getMessage());
}

$updatedCount = 0;

foreach ($empresas as $empresa) {
    $empresaId = $empresa['id'];
    $nombreEmpresa = $empresa['nombre'];
    $logoBD = $empresa['logo'];

    // 2. Generar el patrón de búsqueda para el logo en el filesystem
    // Formato esperado: /assets/img/uploads/logo-{id}/logo-{nombre}-{fecha}-{hash}.png
    $nombreFormateado = preg_replace('/[^a-zA-Z0-9]/', '_', $nombreEmpresa);
    $searchPattern = $baseDir . "logo-" . $empresaId . "/logo-" . $nombreFormateado . "-*";

    // 3. Buscar archivos que coincidan con el patrón
    $files = glob($searchPattern);

    if (!empty($files)) {
        // Tomar el primer archivo encontrado (podrías ordenar por fecha si hay múltiples)
        $nuevoLogoPath = str_replace($baseDir, '/assets/img/uploads/', $files[0]);

        // 4. Comparar con la ruta en la BD
        if ($logoBD !== $nuevoLogoPath) {
            // 5. Actualizar la BD si no coinciden
            try {
                $updateQuery = "UPDATE companies SET logo = :logo WHERE id = :id";
                $db->query($updateQuery);
                $db->bind(':logo', $nuevoLogoPath);
                $db->bind(':id', $empresaId);
                $db->execute();

                echo "Actualizado logo para empresa ID {$empresaId}: {$nuevoLogoPath}\n";
                $updatedCount++;
            } catch (PDOException $e) {
                echo "Error al actualizar empresa ID {$empresaId}: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "No se encontró logo para empresa ID {$empresaId} en el filesystem\n";
    }
}

echo "\nProceso completado. Total de logos actualizados: {$updatedCount}\n";
