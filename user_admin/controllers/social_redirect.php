<?php
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';
$manager = new DatabaseSessionManager();
$conn = $manager->getDB();

// Verifica si el token ha sido recibido
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Consulta para obtener el ID de la empresa utilizando el token
    $stmt = $conn->prepare("SELECT id FROM companies WHERE social_token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['id']) {
        // Si se encuentra la empresa, buscar la red social preferida
        $stmt = $conn->prepare("SELECT url FROM company_social_networks WHERE company_id = :company_id AND red_preferida = 1");
        $stmt->bindParam(':company_id', $result['id']);
        $stmt->execute();
        $social = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($social['url']) {
            // Si se encuentra la red social preferida, redirigir a la página de la red social
            header("Location:" . $social['url']);
        } else {
            echo "No se ha encontrado una red social preferida.";
        }
    } else {
        echo "Token inválido.";
    }
} else {
    echo "No se ha proporcionado ningún token.";
}
