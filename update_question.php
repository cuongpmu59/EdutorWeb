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

    if (!is_numeric($id)) {
        echo "ID không hợp lệ.";
        exit;
    }

    // Lấy ảnh hiện tại
    $stmtGet = $conn->prepare("SELECT image FROM questions WHERE id = :id");
    $stmtGet->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtGet->execute();
    $currentImage = $stmtGet->fetchColumn();

    $imageName = $currentImage;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            echo "Chỉ cho phép ảnh JPG, PNG hoặc GIF.";
            exit;
        }

        $uploadDir = 'images/uploads';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            // Xoá ảnh cũ nếu tồn tại
            if ($currentImage && file_exists($uploadDir . $currentImage)) {
                unlink($uploadDir . $currentImage);
            }
        } else {
            echo "Tải ảnh thất bại.";
            exit;
        }
    }

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
    $stmt->bindParam(':image', $imageName);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Cập nhật câu hỏi thành công.";
    } else {
        echo "Lỗi khi cập nhật câu hỏi.";
    }
}
?>
