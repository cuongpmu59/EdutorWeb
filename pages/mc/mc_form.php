<?php
/**
 * env/dotenv.php
 * Tự động nạp biến môi trường từ file .env
 */

function env($key, $default = null) {
    static $env = null;

    if ($env === null) {
        $env = [];
        $envPath = __DIR__ . '/.env';

        if (!file_exists($envPath)) {
            error_log("[dotenv] ❌ Không tìm thấy file .env tại: $envPath");
            return $default;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;

            [$name, $value] = array_pad(explode('=', $line, 2), 2, null);

            if ($name !== null && $value !== null) {
                $name = trim($name);
                $value = trim($value);

                // Gỡ dấu nháy nếu có
                if (
                    (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))
                ) {
                    $value = substr($value, 1, -1);
                }

                $env[$name] = $value;
            }
        }
    }

    return $env[$key] ?? $default;
}
