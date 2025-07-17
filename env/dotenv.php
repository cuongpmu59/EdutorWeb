<?php
/**
 * env/dotenv.php
 * Tải biến môi trường từ file .env thủ công (không cần thư viện ngoài)
 */

function env($key, $default = null) {
    static $env = null;

    if ($env === null) {
        $env = [];
        $path = __DIR__ . '/.env';

        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#') continue;

                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $name = trim($parts[0]);
                    $value = trim($parts[1]);

                    // Loại bỏ dấu nháy nếu có
                    if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                        (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                        $value = substr($value, 1, -1);
                    }

                    $env[$name] = $value;
                }
            }
        } else {
            error_log("[dotenv] Không tìm thấy file .env tại: $path");
        }
    }

    return $env[$key] ?? $default;
}
