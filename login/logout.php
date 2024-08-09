<?php
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
session_start();
session_destroy();
echo json_encode(['redirect' => $baseUrl]);
exit();
