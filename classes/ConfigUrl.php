<?php

class ConfigUrl
{
    public static function get()
    {
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            return 'http://localhost/agenda/';
        } else {
            return 'https://agenda2024.online/';
        }
    }
}

/**
 * USAR
  require_once __DIR__ . '/classes/ConfigUrl.php';
  $baseUrl = ConfigUrl::get();
 * 
 */
