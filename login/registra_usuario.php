<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();
$creado = false;
$error = "";

if (isset($_POST['usuario']) && isset($_POST['correo']) && isset($_POST['password']) && isset($_POST['password2'])) {
    $usuario_registro = $_POST['usuario'];
    $correo = $_POST['correo'];
    $pass = $_POST['password'];
    $pass2 = $_POST['password2'];
    $company_id = $_POST['company_id'];
    // $master_admin = isset($_POST['master_admin']) ? 1 : 0;

    if (!empty($usuario_registro) && !empty($correo) && !empty($pass) && !empty($pass2) && !empty($company_id)) {
        // Los campos no están vacíos, proceder con la validación y la inserción en la base de datos

        try {
            $query = $conn->prepare("CALL validar_registro(:usuario, :correo, :pass, :pass2, @error)");
            $query->bindParam(':usuario', $usuario_registro);
            $query->bindParam(':correo', $correo);
            $query->bindParam(':pass', $pass);
            $query->bindParam(':pass2', $pass2);
            $query->execute();
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "error" => "Error en la ejecución de la consulta: " . $e->getMessage()]);
            exit;
        }

        // Obtener el mensaje de error desde la variable de sesión de MySQL
        $errorQuery = $conn->query("SELECT @error")->fetch(PDO::FETCH_ASSOC);
        $error = $errorQuery['@error'];

        if ($error) {
            // Mostrar el mensaje de error
            echo json_encode(["success" => false, "error" => $error]);
        } else {
            // El registro es válido, continuar con la inserción en la base de datos
            $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 7]);
            $query = $conn->prepare("INSERT INTO users(name, email, password, company_id) VALUES (:nombre, :correo, :clave, :company_id)");
            $query->bindParam(':nombre', $usuario_registro);
            $query->bindParam(':correo', $correo);
            $query->bindParam(':clave', $hash);
            $query->bindParam(':company_id', $company_id);
            $query->execute();
            $count2 = $query->rowCount();

            if ($count2) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => "Error al agregar el usuario a la base de datos"]);
            }
        }
    } else {
        echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Datos no válidos"]);
}
