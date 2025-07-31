<?php
require_once 'configs/init.php';
require_once 'classes/Database.php';

// Directorio base donde se almacenan los banners
$baseDir = __DIR__ . '/assets/img/banners/';

// 1. Obtener todas las empresas con banners de la base de datos
try {
    $db = new Database();
    $db->query("SELECT id, selected_banner FROM companies WHERE selected_banner IS NOT NULL AND selected_banner != ''");
    $empresas = $db->resultset();

    if (empty($empresas)) {
        throw new Exception("No se encontraron empresas con banners en la base de datos.");
    }
} catch (PDOException $e) {
    die("Error al obtener empresas: " . $e->getMessage());
}

$updatedCount = 0;

foreach ($empresas as $empresa) {
    $empresaId = $empresa['id'];
    $bannerBD = $empresa['selected_banner'];

    // 2. Verificar si el banner ya tiene ruta completa
    if (strpos($bannerBD, '/assets/img/banners/') === 0) {
        continue; // Ya tiene ruta completa, saltar
    }

    // 3. Construir la ruta completa esperada
    $nombreArchivo = basename($bannerBD);
    $rutaCompleta = '/assets/img/banners/user_' . $empresaId . '/' . $nombreArchivo;
    $rutaFisica = $baseDir . 'user_' . $empresaId . '/' . $nombreArchivo;

    // 4. Verificar si el archivo existe físicamente
    if (file_exists($rutaFisica)) {
        // 5. Actualizar la BD con la ruta completa
        try {
            $updateQuery = "UPDATE companies SET selected_banner = :banner WHERE id = :id";
            $db->query($updateQuery);
            $db->bind(':banner', $rutaCompleta);
            $db->bind(':id', $empresaId);
            $db->execute();

            echo "Actualizado banner para empresa ID {$empresaId}: {$rutaCompleta}\n";
            $updatedCount++;
        } catch (PDOException $e) {
            echo "Error al actualizar empresa ID {$empresaId}: " . $e->getMessage() . "\n";
        }
    } else {
        echo "No se encontró el archivo {$nombreArchivo} para empresa ID {$empresaId}\n";
    }
}

echo "\nProceso completado. Total de banners actualizados: {$updatedCount}\n";
