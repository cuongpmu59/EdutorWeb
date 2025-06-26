<?php
$host     = "sql210.infinityfree.com";
$dbname   = "if0_39047715_questionbank";
$username = "if0_39047715";
$password = "Kimdung16091961";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Kết nối thất bại: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
