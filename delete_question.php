<?php
require 'db_connection.php'; // Đảm bảo bạn đã dùng PDO trong file này

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    try {
        // Chuẩn bị và thực thi truy vấn DELETE
        $sql = "DELETE FROM questions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        // Kiểm tra số dòng bị ảnh hưởng để xác nhận xoá thành công
        if ($stmt->rowCount() > 0) {
            echo "Đã xoá thành công.";
        } else {
            echo "Không tìm thấy câu hỏi để xoá.";
        }
    } catch (PDOException $e) {
        echo "Lỗi khi xoá: " . $e->getMessage();
    }
} else {
    echo "Không có ID hợp lệ để xoá.";
}
