<?php
require_once __DIR__ . '/../includes/db_connection.php';
header('Content-Type: application/json');

// Kiểm tra phương thức và mc_id có tồn tại
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra mc_id hợp lệ
    $mc_id = isset($_POST['mc_id']) ? filter_var($_POST['mc_id'], FILTER_VALIDATE_INT) : null;

    if ($mc_id === false || $mc_id === null) {
        echo json_encode(['success' => false, 'message' => '❌ mc_id không hợp lệ.']);
        exit;
    }

    try {
        // Thực hiện xóa
        $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
        $stmt->execute([$mc_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => '✅ Xoá thành công.']);
        } else {
            echo json_encode(['success' => false, 'message' => '❌ Không tìm thấy câu hỏi để xoá.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => '❌ Lỗi truy vấn: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => '❌ Phương thức không hợp lệ.']);
}
