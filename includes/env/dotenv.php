<?php
/**
 * env/dotenv.php
 * Tải biến môi trường từ file .env thủ công (không cần thư viện ngoài)
 */

function env($key, $default = null) {
    static $env = null;

    if ($env === null) {
        $env = [];
        $envPath = realpath(__DIR__ . '/includes/env/.env'); // đảm bảo đúng đường dẫn thực

        if ($envPath && file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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
            error_log("[dotenv] ❌ Không tìm thấy file .env tại: $envPath");
        }
    }

    return $env[$key] ?? $default;
}
