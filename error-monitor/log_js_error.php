<?php
// error-monitor/log_js_error.php
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data) {
    $logLine = "[" . date('Y-m-d H:i:s') . "] " .
        "Page: " . ($data['page'] ?? '') . " | " .
        "Error: " . ($data['error'] ?? '') . " | " .
        "Stack: " . ($data['stack'] ?? '') . " | " .
        "URL: " . ($data['url'] ?? '') . " | " .
        "UserAgent: " . ($data['userAgent'] ?? '') . "\n";
    $logDir = __DIR__ . '/../logs/';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    file_put_contents($logDir . 'js_errors.log', $logLine, FILE_APPEND);
}
http_response_code(204);
