<?php
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();

// Remove JWT from cookies
setcookie('jwt', '', time() - 3600, '/');

header("Location: $baseUrl"); // Redirect to baseUrl
exit();
