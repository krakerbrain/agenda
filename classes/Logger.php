<?php

class Logger
{
    // Ruta del archivo de logs
    private $logFile;

    public function __construct($logFile = __DIR__ . '/logs/errors.log')
    {
        $this->logFile = $logFile;
    }

    // Método para escribir logs en el archivo
    public function logError($message)
    {
        $this->writeLog('ERROR', $message);
    }

    public function logInfo($message)
    {
        $this->writeLog('INFO', $message);
    }

    private function writeLog($level, $message)
    {
        $date = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$date] [$level] $message" . PHP_EOL, FILE_APPEND);
    }
}
