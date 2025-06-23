<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';

    if (!is_numeric($id)) {
        echo "❌ ID không hợp lệ.";
        exit;
    }

    // Lấy đường dẫn ảnh (nếu cần dùng sau này cho Cloudinary API)
    $stmtGet = $conn->prepare("SELECT image FROM questions WHERE id = :id");
    $stmtGet->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtGet->execute();
    $image_url = $stmtGet->fetchColumn();

    // Xoá câu hỏi khỏi CSDL
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "✅ Xoá câu hỏi thành công.";
        // Nếu bạn muốn xoá luôn ảnh trên Cloudinary, bạn cần gửi thêm public_id ảnh từ phía client hoặc lưu trong DB
    } else {
        echo "❌ Lỗi khi xoá câu hỏi.";
    }
}
?>
