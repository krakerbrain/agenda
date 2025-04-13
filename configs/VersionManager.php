<?php
// classes/VersionManager.php

class VersionManager
{
    private static $instance = null;
    private $version = null;

    private function __construct()
    {
        // Privado para evitar instanciación directa
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->calculateVersion();
        }
        return self::$instance;
    }

    private function calculateVersion()
    {
        $baseDir = dirname(__DIR__); // Ajusta según tu estructura
        $jsDir = $baseDir . '/assets/js/';

        // Versión base (este archivo + navbar)
        $this->version = max(
            filemtime(__FILE__),
            filemtime($baseDir . '/index.php') // Tu navbar principal
        );

        // Escaneo recursivo de archivos JS (con caché en desarrollo)
        if ($this->isDevelopment()) {
            $this->version = max($this->version, $this->getCachedJsVersion($jsDir));
        } else {
            $this->version = max($this->version, $this->scanJsFiles($jsDir));
        }
    }

    private function scanJsFiles($dir)
    {
        $latestTime = 0;

        if (!is_dir($dir)) return $latestTime;

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'js') {
                $latestTime = max($latestTime, $file->getMTime());
            }
        }

        return $latestTime;
    }

    private function getCachedJsVersion($dir)
    {
        $cacheFile = sys_get_temp_dir() . '/app_js_version.cache';
        $cacheTime = 60; // 1 minuto de caché

        if (file_exists($cacheFile)) {
            $cacheData = json_decode(file_get_contents($cacheFile), true);
            if (time() - $cacheData['timestamp'] < $cacheTime) {
                return $cacheData['version'];
            }
        }

        $version = $this->scanJsFiles($dir);
        file_put_contents($cacheFile, json_encode([
            'version' => $version,
            'timestamp' => time()
        ]));

        return $version;
    }

    private function isDevelopment()
    {
        // Puedes ajustar esta lógica según tu entorno
        return $_SERVER['SERVER_NAME'] === 'localhost' ||
            $_SERVER['SERVER_ADDR'] === '127.0.0.1';
    }

    public function getVersion()
    {
        return $this->version ?: time();
    }

    // Métodos para prevenir clonación y deserialización
    public function __clone()
    {
        throw new \Exception("Cloning of VersionManager is not allowed");
    }

    public function __wakeup()
    {
        throw new \Exception("Unserializing of VersionManager is not allowed");
    }
}
