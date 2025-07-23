<?php
// Bắt đầu session nếu cần dùng kiểm tra quyền sau này
session_start();

// Kết nối CSDL
require_once __DIR__ . '/db_connection.php';

// Trả về JSON
header('Content-Type: application/json');

// Lấy ID từ query string
$mc_id = isset($_GET['mc_id']) ? (int)$_GET['mc_id'] : 0;

if ($mc_id <= 0) {
    echo json_encode(['error' => 'ID không hợp lệ']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);
    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($question) {
        echo json_encode($question);
    } else {
        echo json_encode(['error' => 'Không tìm thấy câu hỏi']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Lỗi truy vấn: ' . $e->getMessage()]);
}
