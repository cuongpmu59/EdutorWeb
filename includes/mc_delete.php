<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../includes/db_connection.php';

try {
    // Đọc dữ liệu JSON
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['mc_id'])) {
        throw new Exception("Thiếu ID câu hỏi cần xoá.");
    }

    $mc_id = intval($input['mc_id']);

    // Lấy thông tin ảnh để xóa file nếu có
    $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        throw new Exception("Không tìm thấy câu hỏi cần xoá.");
    }

    $imagePath = $row['mc_image_url'];

    // Xoá câu hỏi trong CSDL
    $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
    $success = $stmt->execute([$mc_id]);

    if (!$success) {
        throw new Exception("Không thể xoá câu hỏi.");
    }

    // Xoá ảnh vật lý nếu tồn tại
    if ($imagePath) {
        $fullPath = __DIR__ . '/../' . $imagePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
