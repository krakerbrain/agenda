<?php

// CompanyController.php
require_once dirname(__DIR__, 2) . '/classes/CompanyModel.php';

class CompanyController
{
    private $companyModel;

    public function __construct()
    {
        $this->companyModel = new CompanyModel();
    }

    public function getCompanyData($token)
    {
        $company = $this->companyModel->getCompanyByToken($token);

        if (!$company) {
            http_response_code(404);
            echo json_encode(["error" => "Company not found"]);
            exit();
        }

        $services = $this->companyModel->getServicesByCompanyId($company['id']);
        $socialNetworks = $this->companyModel->getSocialNetworksByCompanyId($company['id']);

        return [
            "company" => $company,
            "services" => $services,
            "socialNetworks" => $socialNetworks,
            "style" => [
                "primary_color" =>      $company['font_color'] ?? '#525252',
                "secondary_color" =>    $company['btn2'] ?? '#9b80ff',
                "background_color" =>   $company['bg_color'] ?? '#bebdff',
                "button_color" =>       $company['btn1'] ?? '#ffffff',
                "border_color" =>       $company['font_color'] ?? '#525252',
            ]
        ];
    }
}
