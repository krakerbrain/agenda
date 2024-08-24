<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();
$creado = false;
$error = "";

if (isset($_POST['usuario']) && isset($_POST['correo']) && isset($_POST['password']) && isset($_POST['password2'])) {
    $data = [
        'role' => $_POST['role_id'],
        'usuario' => $_POST['usuario'],
        'correo' => $_POST['correo'],
        'password' => $_POST['password'],
        'password2' => $_POST['password2'],
        'company_id' => $_POST['company_id']
    ];

    if ($data['role'] != 1) {
        echo json_encode(["success" => false, "error" => "Error en asignación de rol. Rol no válido"]);
        exit;
    }

    if (!empty($data['usuario']) && !empty($data['correo']) && !empty($data['password']) && !empty($data['password2']) && !empty($data['company_id'])) {
        // Los campos no están vacíos, proceder con la validación y la inserción en la base de datos

        try {
            $query = $conn->prepare("CALL validar_registro(:usuario, :correo, :pass, :pass2, @error)");
            $query->bindParam(':usuario', $data['usuario']);
            $query->bindParam(':correo', $data['correo']);
            $query->bindParam(':pass', $data['password']);
            $query->bindParam(':pass2', $data['password2']);
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
            $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 7]);
            $token = hash('sha256', $data['usuario'] . $data['correo']);
            $query = $conn->prepare("INSERT INTO users(name, email, password, company_id, role_id, token_sha256, created_at) VALUES (:nombre, :correo, :clave, :company_id, :role, :token, NOW())");
            $query->bindParam(':nombre', $data['usuario']);
            $query->bindParam(':correo', $data['correo']);
            $query->bindParam(':clave', $hash);
            $query->bindParam(':company_id', $data['company_id']);
            $query->bindParam(':role', $data['role']);
            $query->bindParam(':token', $token);
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
