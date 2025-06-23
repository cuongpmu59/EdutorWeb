<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = $_POST['question'] ?? '';
    $answer1 = $_POST['answer1'] ?? '';
    $answer2 = $_POST['answer2'] ?? '';
    $answer3 = $_POST['answer3'] ?? '';
    $answer4 = $_POST['answer4'] ?? '';
    $correct_answer = $_POST['correct_answer'] ?? '';
    $image_url = $_POST['image_url'] ?? ''; // ✅ Đường dẫn ảnh từ JS gửi

    // Kiểm tra input cơ bản
    if (empty($question) || empty($answer1) || empty($correct_answer)) {
        echo "❌ Vui lòng nhập đầy đủ thông tin câu hỏi.";
        exit;
    }

    try {
        $sql = "INSERT INTO questions 
                (question, answer1, answer2, answer3, answer4, correct_answer, image)
                VALUES (:question, :answer1, :answer2, :answer3, :answer4, :correct_answer, :image)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':answer1', $answer1);
        $stmt->bindParam(':answer2', $answer2);
        $stmt->bindParam(':answer3', $answer3);
        $stmt->bindParam(':answer4', $answer4);
        $stmt->bindParam(':correct_answer', $correct_answer);
        $stmt->bindParam(':image', $image_url); // ✅ Ghi URL Cloudinary

        if ($stmt->execute()) {
            echo "✅ Thêm câu hỏi thành công.";
        } else {
            echo "❌ Lỗi khi thêm câu hỏi.";
        }
    } catch (PDOException $e) {
        echo "❌ PDO Error: " . $e->getMessage();
    }
}
?>
