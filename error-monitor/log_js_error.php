<?php
// error-monitor/log_js_error.php
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data) {
    // Definir nombre del archivo según el contexto
    $context = isset($data['context']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $data['context']) : 'default_log';
    $logDir = __DIR__ . '/../logs/';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $logFile = $logDir . $context . '.log';

    // Armar línea de log con todos los datos
    $logLine = "[" . date('Y-m-d H:i:s') . "] " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";

    file_put_contents($logFile, $logLine, FILE_APPEND);
}

http_response_code(204);