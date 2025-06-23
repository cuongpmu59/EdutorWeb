<?php
require 'db_connection.php'; // Kết nối CSDL

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form (JS gửi qua AJAX)
    $question = $_POST['question'] ?? '';
    $answer1 = $_POST['answer1'] ?? '';
    $answer2 = $_POST['answer2'] ?? '';
    $answer3 = $_POST['answer3'] ?? '';
    $answer4 = $_POST['answer4'] ?? '';
    $correct_answer = $_POST['correct_answer'] ?? '';
    $image_url = $_POST['image_url'] ?? ''; // ✅ Đường dẫn ảnh đã upload lên Cloudinary

    // Kiểm tra dữ liệu đầu vào
    if (empty($question) || empty($answer1) || empty($correct_answer)) {
        echo "❌ Vui lòng nhập đầy đủ thông tin câu hỏi và ít nhất đáp án A.";
        exit;
    }

    try {
        // Tạo câu lệnh SQL thêm mới
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
        $stmt->bindParam(':image', $image_url); // URL ảnh từ Cloudinary hoặc chuỗi rỗng

        if ($stmt->execute()) {
            echo "✅ Thêm câu hỏi thành công.";
            if (!empty($image_url)) {
                echo "<br><a href='" . htmlspecialchars($image_url) . "' target='_blank'>🖼️ Xem ảnh minh họa</a><br>";
                echo "<img src='" . htmlspecialchars($image_url) . "' alt='Ảnh minh họa' style='max-width:200px; max-height:200px; display:block; margin-top:10px; border:1px solid #ccc; border-radius:4px;' />";
            }
        } else {
            echo "❌ Có lỗi xảy ra khi lưu câu hỏi.";
        }
    } catch (PDOException $e) {
        echo "❌ Lỗi PDO: " . $e->getMessage();
    }
} else {
    echo "❌ Phương thức không hợp lệ.";
}
?>
