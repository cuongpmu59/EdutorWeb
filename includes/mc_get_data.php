<?php
require_once __DIR__ . '/../../includes/db_connection.php';

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Nếu có tham số mc_id => truy vấn 1 câu hỏi
    if (isset($_GET['mc_id'])) {
        $mc_id = intval($_GET['mc_id']);

        if ($mc_id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'mc_id không hợp lệ'
            ]);
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
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
        exit;
    }

    // Nếu không có mc_id => trả về toàn bộ danh sách rút gọn
    $stmt = $conn->query("SELECT mc_id, mc_topic, mc_question FROM multiple_choice ORDER BY mc_id DESC");
    $rows = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $rows
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $e->getMessage()
    ]);
}
