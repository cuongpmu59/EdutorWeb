<?php
require 'db_connection.php';

// Nhận dữ liệu từ form
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$question = $_POST['question'];
$answer1 = $_POST['answer1'];
$answer2 = $_POST['answer2'];
$answer3 = $_POST['answer3'];
$answer4 = $_POST['answer4'];
$correct_answer = intval($_POST['correct_answer']);

// Xử lý upload ảnh nếu có
$image_path = '';
$upload_dir = 'images/uploads/';

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['image']['tmp_name'];
    $original_name = basename($_FILES['image']['name']);
    $ext = pathinfo($original_name, PATHINFO_EXTENSION);
    $unique_name = uniqid('img_') . '.' . $ext;
    $target_path = $upload_dir . $unique_name;

    // Kiểm tra và tạo thư mục nếu chưa có
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (move_uploaded_file($tmp_name, $target_path)) {
        $image_path = $target_path;
    }
}

// Nếu cập nhật, lấy lại đường dẫn ảnh cũ nếu không upload mới
if ($id > 0 && $image_path == '') {
    $stmt = $conn->prepare("SELECT image FROM questions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($existing_image);
    if ($stmt->fetch()) {
        $image_path = $existing_image;
    }
    $stmt->close();
}

// Thêm hoặc cập nhật
if ($id > 0) {
    // Cập nhật
    $stmt = $conn->prepare("UPDATE questions SET question=?, image=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=? WHERE id=?");
    $stmt->bind_param("ssssssii", $question, $image_path, $answer1, $answer2, $answer3, $answer4, $correct_answer, $id);
} else {
    // Thêm mới
    $stmt = $conn->prepare("INSERT INTO questions (question, image, answer1, answer2, answer3, answer4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi",_
