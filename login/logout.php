<?php
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
session_start();
session_destroy();
header("Location: " . $baseUrl . "user_admin/index.php");
exit();
