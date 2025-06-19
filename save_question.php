<?php
require 'db_connection.php'; // Kết nối CSDL

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Hàm làm sạch chuỗi nhập vào
    function cleanInput($data) {
        return htmlspecialchars(trim($data));
    }

    // Nhận và làm sạch dữ liệu từ form
    $question = cleanInput($_POST['question'] ?? '');
    $answer1 = cleanInput($_POST['answer1'] ?? '');
    $answer2 = cleanInput($_POST['answer2'] ?? '');
    $answer3 = cleanInput($_POST['answer3'] ?? '');
    $answer4 = cleanInput($_POST['answer4'] ?? '');
    $correct_answer = $_POST['correct_answer'] ?? '';
    $image_path = '';

    // Kiểm tra bắt buộc
    if (empty($question) || empty($answer1) || empty($answer2) || empty($correct_answer)) {
        die("Vui lòng nhập đầy đủ các trường bắt buộc (câu hỏi, đáp án A, B và đáp án đúng).");
    }

    // Kiểm tra giá trị hợp lệ cho correct_answer
    $valid_answers = ['answer1', 'answer2', 'answer3', 'answer4'];
    if (!in_array($correct_answer, $valid_answers)) {
        die("Đáp án đúng không hợp lệ.");
    }

    // Xử lý ảnh nếu có
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = "images/uploads";
        $fileTmpPath = $_FILES["image"]["tmp_name"];
        $originalName = basename($_FILES["image"]["name"]);
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        // Kiểm tra định dạng ảnh
        if (!in_array($fileExt, $allowedExts)) {
            die("Chỉ cho phép ảnh có định dạng jpg, jpeg, png, gif.");
        }

        // Giới hạn kích thước (ví dụ 2MB)
        if ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
            die("Ảnh vượt quá dung lượng cho phép (2MB).");
        }

        // Tạo tên mới tránh trùng
        $newFileName = uniqid('img_', true) . '.' . $fileExt;
        $targetPath = $uploadDir . $newFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($fileTmpPath, $targetPath)) {
            die("Lỗi khi lưu ảnh lên máy chủ.");
        }

        $image_path = $newFileName;
    }

    // Chuẩn bị và thực hiện câu lệnh SQL
    $sql = "INSERT INTO questions (question, answer1, answer2, answer3, answer4, correct_answer, image)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
    }
    $stmt->bind_param("sssssss", $question, $answer1, $answer2, $answer3, $answer4, $correct_answer, $image_path);

    if ($stmt->execute()) {
        // Trả về thông báo thành công nếu gửi bằng fetch AJAX hoặc redirect
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            echo "Thêm câu hỏi thành công!";
        } else {
            header("Location: question_form.php");
            exit;
        }
    } else {
        echo "Lỗi khi lưu câu hỏi: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Phương thức gửi không hợp lệ.";
}
