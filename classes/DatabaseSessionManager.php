<?php
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();
class DatabaseSessionManager
{
    private $db;
    public $companyId;

    public function __construct()
    {
        $this->startSession();
        $this->connectDB();
        $this->companyId = $_SESSION['company_id'] ?? null;
    }

    public function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function connectDB()
    {
        $servername = $_ENV['HOST'];
        $username = $_ENV['USUARIO'];
        $password = $_ENV['PASS'];
        $dbname = $_ENV['BD'];

        try {
            $this->db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getDB()
    {
        return $this->db;
    }

    public function setCompanyId($companyId)
    {
        $_SESSION['company_id'] = $companyId;
        $this->companyId = $companyId;
    }
}
