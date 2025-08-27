<?php
require_once __DIR__ . '/../env/dotenv.php'; 

$host    = env('DB_HOST', 'localhost');
$db      = env('DB_NAME', 'test');
$user    = env('DB_USER', 'root');
$pass    = env('DB_PASS', '');
$charset = env('DB_CHARSET', 'utf8');

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Trả về lỗi JSON nếu dùng qua API/AJAX, hoặc có thể đổi thành die(...) nếu dùng giao diện
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status'  => 'error',
        'message' => '❌ Kết nối CSDL thất bại: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
