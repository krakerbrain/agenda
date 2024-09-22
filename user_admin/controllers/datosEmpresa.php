<?php
require_once dirname(__DIR__, 2) . '/classes/CompanyManager.php';
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/jwt.php';

$baseUrl = ConfigUrl::get();
$datosUsuario = validarToken();
if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
    exit();
}
header('Content-Type: application/json');
try {
    $companyManager = new CompanyManager();
    $company_id = $datosUsuario['company_id'];
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode(['success' => true, 'data' => $companyManager->getCompanyDataForDatosEmpresa($company_id)]);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Capturar los datos de la empresa desde $_POST
        $phone = $_POST['phone'] ?? null;
        $address = $_POST['address'] ?? null;
        $description = $_POST['description'] ?? null;
        $logoUrl = $_POST['logo_url'] ?? null;  // Logo existente si no se sube uno nuevo

        // Manejo del archivo de imagen (logo) en $_FILES
        $logoName = $logoUrl;  // Mantener el logo anterior si no hay nuevo

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $fileManager = new FileManager();
            $logoName = $fileManager->uploadLogo($_POST['company_name'], $company_id);
        }

        // Actualizar los datos de la empresa
        $data = [
            'phone' => $phone,
            'address' => $address,
            'description' => $description,
            'logo' => $logoName
        ];

        $companyManager->updateCompanyData($company_id, $data);

        echo json_encode(['success' => true, 'message' => 'Datos de la empresa actualizados correctamente']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}



// Acceder a los datos de texto enviados por formData
// $company_id = $_POST['company_id'] ?? null;
// $name = $_POST['company_name'] ?? null;
// $phone = $_POST['phone'] ?? null;
// $address = $_POST['address'] ?? null;
// $description = $_POST['description'] ?? null;
// // Crear una instancia de FileManages
// $fileManager = new FileManager();
// try {
//     $conn->beginTransaction();

//     // Aquí manejarías la subida del archivo como en el ejemplo anterior
//     $logo = !empty($_FILES['logo']['name']) ? $fileManager->uploadLogo($name, $company_id) : $_POST['logo_url'];

//     // Actualizar los datos de la empresa
//     $sql = $conn->prepare("UPDATE companies SET logo = :logo, phone = :phone, address = :address, description = :description WHERE id = :id");
//     $sql->bindParam(':phone', $phone);
//     $sql->bindParam(':address', $address);
//     $sql->bindParam(':description', $description);
//     $sql->bindParam(':logo', $logo);
//     $sql->bindParam(':id', $company_id);
//     $sql->execute();

//     $conn->commit();
//     echo json_encode(['success' => true, 'message' => 'Datos de la empresa actualizados correctamente']);
// } catch (Exception $e) {
//     $conn->rollBack();
//     echo json_encode(['success' => false, 'error' => 'Error al agregar la empresa: ' . $e->getMessage()]);
// }