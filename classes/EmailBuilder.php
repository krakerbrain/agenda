<?php

class EmailBuilder
{
    private $templatePath;

    public function __construct($baseUrl)
    {
        $this->templatePath = $baseUrl . 'correos_template/';
    }

    public function buildTemplate($templateType, $placeholders)
    {
        $templateFile = $this->templatePath . "correo_{$templateType}.php";

        // if (!file_exists($templateFile)) {
        //     throw new Exception("Plantilla {$templateType} no encontrada.");
        // }

        $templateContent = file_get_contents($templateFile);
        return str_replace(array_keys($placeholders), array_values($placeholders), $templateContent);
    }
}
