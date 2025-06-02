<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Làm sạch dữ liệu đầu vào
    function cleanInput($data) {
        return htmlspecialchars(trim($data));
    }

    $id = intval($_POST['id'] ?? 0);
    $question = cleanInput($_POST['question'] ?? '');
    $answer1 = cleanInput($_POST['answer1'] ?? '');
    $answer2 = cleanInput($_POST['answer2'] ?? '');
    $answer3 = cleanInput($_POST['answer3'] ?? '');
    $answer4 = cleanInput($_POST['answer4'] ?? '');
    $correct_answer = $_POST['correct_answer'] ?? '';
    $image_path = '';

    // Kiểm tra dữ liệu
    if ($id <= 0 || empty($question) || empty($answer1) || empty($answer2) || empty($correct_answer)) {
        die("Thiếu thông tin bắt buộc.");
    }

    $valid_answers = ['answer1', 'answer2', 'answer3', 'answer4'];
    if (!in_array($correct_answer, $valid_answers)) {
        die("Đáp án đúng không hợp lệ.");
    }

    // Lấy ảnh cũ từ DB để có thể xóa nếu có ảnh mới
    $old_image = '';
    $checkStmt = $conn->prepare("SELECT image FROM questions WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $checkStmt->bind_result($old_image);
    $checkStmt->fetch();
    $checkStmt->close();

    // Xử lý ảnh mới (nếu có)
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = "images/";
        $fileTmpPath = $_FILES["image"]["tmp_name"];
        $originalName = basename($_FILES["image"]["name"]);
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExt, $allowedExts)) {
            die("Chỉ hỗ trợ định dạng ảnh jpg, jpeg, png, gif.");
        }

        if ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
            die("Ảnh vượt quá kích thước 2MB.");
        }

        $newFileName = uniqid('img_', true) . '.' . $fileExt;
        $targetPath = $uploadDir . $newFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($fileTmpPath, $targetPath)) {
            die("Lỗi khi tải ảnh lên.");
        }

        $image_path = $newFileName;

        // Xóa ảnh cũ nếu tồn tại
        if (!empty($old_image)) {
            $oldImagePath = $uploadDir . $old_image;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    }

    // Thực hiện câu lệnh UPDATE
    if (empty($image_path)) {
        $stmt = $conn->prepare("UPDATE questions 
            SET question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=? 
            WHERE id=?");
        $stmt->bind_param("ssssssi", $question, $answer1, $answer2, $answer3, $answer4, $correct_answer, $id);
    } else {
        $stmt = $conn->prepare("UPDATE questions 
            SET question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=?, image=? 
            WHERE id=?");
        $stmt->bind_param("sssssssi", $question, $answer1, $answer2, $answer3, $answer4, $correct_answer, $image_path, $id);
    }

    if ($stmt->execute()) {
        header("Location: question_form.php");
        exit;
    } else {
        echo "Lỗi khi cập nhật câu hỏi: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Phương thức gửi không hợp lệ.";
}
