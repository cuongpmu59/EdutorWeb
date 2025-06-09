<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';

    // Xoá ảnh trước nếu có
    $stmtGet = $conn->prepare("SELECT image FROM questions WHERE id = :id");
    $stmtGet->bindParam(':id', $id);
    $stmtGet->execute();
    $image = $stmtGet->fetchColumn();

    if ($image && file_exists('images/' . $image)) {
        unlink('images/' . $image);
    }

    $stmt = $conn->prepare("DELETE FROM questions WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "Xoá câu hỏi thành công.";
    } else {
        echo "Lỗi khi xoá câu hỏi.";
    }
}
?>
