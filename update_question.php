<?php
require 'db_connection.php'; // Kết nối CSDL

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $id = $_POST['id'] ?? '';
    $question = $_POST['question'] ?? '';
    $answer1 = $_POST['answer1'] ?? '';
    $answer2 = $_POST['answer2'] ?? '';
    $answer3 = $_POST['answer3'] ?? '';
    $answer4 = $_POST['answer4'] ?? '';
    $correct_answer = $_POST['correct_answer'] ?? '';
    $topic = $_POST['topic'] ?? '';
    $deleteImage = $_POST['delete_image'] ?? '0'; // "1" nếu checkbox xóa ảnh được chọn
    $image_url = $_POST['image_url'] ?? '';        // URL ảnh từ Cloudinary

    // Kiểm tra ID
    if (!is_numeric($id)) {
        echo "❌ ID không hợp lệ.";
        exit;
    }

    // Lấy ảnh hiện tại từ CSDL
    $stmtGet = $conn->prepare("SELECT image FROM questions WHERE id = :id");
    $stmtGet->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtGet->execute();
    $currentImage = $stmtGet->fetchColumn();

    // Xử lý ảnh
    if ($deleteImage === '1') {
        $image_url = ''; // Xoá ảnh
    } elseif (empty($image_url)) {
        $image_url = $currentImage; // Giữ nguyên ảnh cũ nếu không có ảnh mới
    }

    try {
        // Câu lệnh UPDATE
        $sql = "UPDATE questions SET
                    question = :question,
                    answer1 = :answer1,
                    answer2 = :answer2,
                    answer3 = :answer3,
                    answer4 = :answer4,
                    correct_answer = :correct_answer,
                    topic = :topic,
                    image = :image
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':answer1', $answer1);
        $stmt->bindParam(':answer2', $answer2);
        $stmt->bindParam(':answer3', $answer3);
        $stmt->bindParam(':answer4', $answer4);
        $stmt->bindParam(':correct_answer', $correct_answer);
        $stmt->bindParam(':topic', $topic);
        $stmt->bindParam(':image', $image_url);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "✅ Cập nhật câu hỏi thành công.";
            if (!empty($image_url)) {
                echo "<br><a href='" . htmlspecialchars($image_url) . "' target='_blank'>🖼️ Xem ảnh minh họa</a><br>";
                echo "<img src='" . htmlspecialchars($image_url) . "' alt='Ảnh minh họa' style='max-width:200px; max-height:200px; display:block; margin-top:10px; border:1px solid #ccc; border-radius:4px;' />";
            }
        } else {
            echo "❌ Cập nhật thất bại.";
        }
    } catch (PDOException $e) {
        echo "❌ Lỗi PDO: " . $e->getMessage();
    }
} else {
    echo "❌ Phương thức không hợp lệ.";
}
?>
