<?php
require 'db_connection.php'; // Sử dụng file kết nối mới

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $sql = "DELETE FROM questions WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Đã xoá thành công.";
    } else {
        echo "Lỗi khi xoá: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Không có ID hợp lệ để xoá.";
}
