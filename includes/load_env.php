<?php
function loadEnv($path = __DIR__ . '/../env/.env') { // Đường dẫn mới
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = array_map('trim', explode('=', $line, 2));
        $value = trim($value, '"\'');
        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
}
