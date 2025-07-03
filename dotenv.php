<?php
// dotenv.php – giúp load biến từ file .env nếu không dùng Composer

if (!function_exists('getenv')) {
    function getenv($key) {
        $env = parse_ini_file(__DIR__ . '/.env');
        return $env[$key] ?? null;
    }
}
