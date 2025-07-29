<?php
session_start();
require_once __DIR__ . '/db_connection.php';

// ✅ Kiểm tra phương thức gọi
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('❌ Phương thức không hợp lệ.');
}

// ✅ Lấy và kiểm tra mc_id
$mc_id = (int)($_POST['mc_id'] ?? 0);
if (!$mc_id) {
    http_response_code(400);
    exit('❌ Thiếu hoặc không hợp lệ mc_id.');
}

try {
    // ✅ Lấy đường dẫn ảnh nếu có
    $stmt = $conn->prepare("SELECT mc_image_url FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        exit('❌ Không tìm thấy câu hỏi.');
    }

    // ✅ Xoá file ảnh trên ổ đĩa (nếu có)
    if (!empty($row['mc_image_url'])) {
        $imagePath = realpath(__DIR__ . '/../' . $row['mc_image_url']);
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // ✅ Xoá bản ghi khỏi CSDL
    $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);

    echo '✅ Đã xoá thành công';
} catch (Exception $e) {
    error_log("❌ Lỗi xoá câu hỏi: " . $e->getMessage());
    http_response_code(500);
    echo '❌ Đã xảy ra lỗi khi xoá.';
}
