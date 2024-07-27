<?php
class Services
{
    private $conn;
    private $company_id;
    public function __construct($conn, $company_id)
    {
        $this->conn = $conn;
        $this->company_id = $company_id;
    }
    public function getServices()
    {
        $servicesSql = $this->conn->prepare("
            SELECT s.id AS service_id, s.name AS service_name, s.duration, s.observations, 
                   sc.id AS category_id, sc.category_name, sc.category_description
            FROM services s
            LEFT JOIN service_categories sc ON s.id = sc.service_id
            WHERE s.company_id = :company_id
        ");
        $servicesSql->bindParam(':company_id', $this->company_id);
        $servicesSql->execute();
        $servicesData = $servicesSql->fetchAll(PDO::FETCH_ASSOC);

        //Devolver los datos en un json para manejarlos en el front

        return json_encode($servicesData);
    }
}
