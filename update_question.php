<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $question = $_POST['question'] ?? '';
    $answer1 = $_POST['answer1'] ?? '';
    $answer2 = $_POST['answer2'] ?? '';
    $answer3 = $_POST['answer3'] ?? '';
    $answer4 = $_POST['answer4'] ?? '';
    $correct_answer = $_POST['correct_answer'] ?? '';
    $deleteImage = $_POST['delete_image'] ?? '0';
    $image_url = $_POST['image_url'] ?? ''; // ✅ đường dẫn Cloudinary từ JS

    if (!is_numeric($id)) {
        echo "❌ ID không hợp lệ.";
        exit;
    }

    // Lấy ảnh hiện tại (lưu dạng URL)
    $stmtGet = $conn->prepare("SELECT image FROM questions WHERE id = :id");
    $stmtGet->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtGet->execute();
    $currentImage = $stmtGet->fetchColumn();

    // Nếu xoá ảnh
    if ($deleteImage === '1') {
        $image_url = ''; // ✅ gán lại rỗng
    } elseif (empty($image_url)) {
        // Nếu không gửi ảnh mới, giữ ảnh cũ
        $image_url = $currentImage;
    }

    // Cập nhật dữ liệu
    $sql = "UPDATE questions SET
            question = :question,
            answer1 = :answer1,
            answer2 = :answer2,
            answer3 = :answer3,
            answer4 = :answer4,
            correct_answer = :correct_answer,
            image = :image
            WHERE id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':question', $question);
    $stmt->bindParam(':answer1', $answer1);
    $stmt->bindParam(':answer2', $answer2);
    $stmt->bindParam(':answer3', $answer3);
    $stmt->bindParam(':answer4', $answer4);
    $stmt->bindParam(':correct_answer', $correct_answer);
    $stmt->bindParam(':image', $image_url); // ✅ Cloudinary URL
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "✅ Cập nhật câu hỏi thành công.";
        if (!empty($image_url)) {
            echo "<br><a href='" . htmlspecialchars($image_url) . "' target='_blank'>🖼️ Xem ảnh minh họa</a>";
        }
    } else {
        echo "❌ Lỗi khi cập nhật câu hỏi.";
    }
}
?>

