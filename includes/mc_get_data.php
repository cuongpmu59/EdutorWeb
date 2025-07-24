<?php
require_once '../db_connection.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kiểm tra tham số
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

try {
    $stmt = $conn->prepare("SELECT * FROM multiple_choice WHERE mc_id = ?");
    $stmt->execute([$mc_id]);
    $row = $stmt->fetch();

    if ($row) {
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
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $e->getMessage()
    ]);
}
