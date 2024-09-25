<?php

define('BASE_PATH', dirname(__DIR__)); // Ajusta BASE_PATH según tu estructura
require BASE_PATH . '/vendor/autoload.php'; // Carga autoloader de Composer

// Inicializa y carga las variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();
