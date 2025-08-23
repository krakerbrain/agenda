<?php
// error-monitor/logger.php
function logErrorToFile($filename, $message)
{
    $logDir = __DIR__ . '/../logs/';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $logLine = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
    file_put_contents($logDir . $filename, $logLine, FILE_APPEND);
}
