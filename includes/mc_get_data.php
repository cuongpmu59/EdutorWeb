<?php
require_once '../db_connection.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1); // Bật hiển thị lỗi khi debug

// Kiểm tra tham số mc_id có tồn tại không
if (!isset($_GET['mc_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu tham số mc_id'
    ]);
    exit;
}

$mc_id = intval($_GET['mc_id']);

if ($mc_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'mc_id không hợp lệ'
    ]);
    exit;
}

// Chuẩn bị và thực thi truy vấn
$stmt = $conn->prepare("SELECT * FROM multiple_choice WHERE mc_id = ?");
$stmt->bind_param("i", $mc_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'data' => $row
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy câu hỏi.'
    ]);
}

$stmt->close();
$conn->close();
