<?php
// Thay bằng thông tin thật của bạn
$servername = "sql210.infinityfree.com"; 
$username = "if0_39047715";
$password = "Kimdung16091961";
$dbname = "if0_39047715_questionbank";

// Kết nối
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy dữ liệu từ form
$question = $_POST['question'];
$answer1 = $_POST['answer1'];
$answer2 = $_POST['answer2'];
$answer3 = $_POST['answer3'];
$answer4 = $_POST['answer4'];
$correct_answer = $_POST['correct_answer'];

// Xử lý ảnh
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $file_name = time() . "_" . basename($_FILES['image']['name']);
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = $target_file;
    }
}

// Thêm dữ liệu vào bảng
$stmt = $conn->prepare("INSERT INTO questionbank (question, image, answer1, answer2, answer3, answer4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $question, $image_path, $answer1, $answer2, $answer3, $answer4, $correct_answer);

if ($stmt->execute()) {
    echo "✅ Câu hỏi đã được lưu thành công!";
} else {
    echo "❌ Lỗi: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
