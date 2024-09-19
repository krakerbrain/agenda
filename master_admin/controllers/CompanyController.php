<?php
require_once dirname(__DIR__, 2) . '/classes/ConfigUrl.php';
require_once dirname(__DIR__, 2) . '/access-token/seguridad/jwt.php';
require_once dirname(__DIR__, 2) . '/classes/DatabaseSessionManager.php';

class CompanyController
{
    private $conn;

    public function __construct()
    {
        $manager = new DatabaseSessionManager();
        $this->conn = $manager->getDB();
    }

    public function getCompanies()
    {
        $sql = $this->conn->prepare("SELECT id, name, logo, is_active, token FROM companies");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function handleRequest()
    {
        $datosUsuario = validarTokenSuperUser();
        if (!$datosUsuario) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $companies = $this->getCompanies();
        echo json_encode($companies);
    }
}

$controller = new CompanyController();
$controller->handleRequest();
