<?php

class ConfigUrl
{
    public static function get()
    {
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            return 'http://localhost/agenda/';
        } else {
            return 'https://agendarium.com/';
        }
    }
}

/**
 * USAR
  require_once __DIR__ . '/classes/ConfigUrl.php';
  $baseUrl = ConfigUrl::get();
 * 
 */
