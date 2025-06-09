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

    // Lấy ảnh hiện tại nếu không upload ảnh mới
    $stmtGet = $conn->prepare("SELECT image FROM questions WHERE id = :id");
    $stmtGet->bindParam(':id', $id);
    $stmtGet->execute();
    $currentImage = $stmtGet->fetchColumn();

    $imageName = $currentImage;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'images/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);

        // Xoá ảnh cũ nếu có
        if ($currentImage && file_exists($uploadDir . $currentImage)) {
            unlink($uploadDir . $currentImage);
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
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "Cập nhật câu hỏi thành công.";
    } else {
        echo "Lỗi khi cập nhật câu hỏi.";
    }
}
?>
