<?php
require 'db_connection.php';
header("Content-Type: application/json; charset=utf-8");

// ==== Lấy và kiểm tra ID ====
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'ID không hợp lệ.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ==== Kiểm tra tồn tại câu hỏi ====
$stmt = $conn->prepare("SELECT id FROM questions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Không tìm thấy câu hỏi.'
    ], JSON_UNESCAPED_UNICODE);
    $stmt->close();
    exit;
}
$stmt->close();

// ==== Tiến hành xoá ====
try {
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'status' => 'success',
        'message' => '✅ Đã xoá câu hỏi thành công.'
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi khi xoá: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
