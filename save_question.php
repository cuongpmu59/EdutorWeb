<?php
// Cấu hình kết nối MySQL - thay thông tin tương ứng trên InfinityFree
$host = "sql210.infinityfree.com"; // ví dụ: sql304.epizy.com
$dbname = "if0_39047715_questionbank";
$username = "if0_39047715";
$password = "Kimdung16091961";

// Kết nối
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nhận dữ liệu từ form
$question = $_POST['question'];
$answer1 = $_POST['answer1'];
$answer2 = $_POST['answer2'];
$answer3 = $_POST['answer3'] ?? null;
$answer4 = $_POST['answer4'] ?? null;
$correct = $_POST['correct_answer'];

// Xử lý ảnh nếu có
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "images/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imagePath = $targetFile;
    }
}

// Chuẩn bị truy vấn SQL
$stmt = $conn->prepare("INSERT INTO questions (question, image, answer1, answer2, answer3, answer4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $question, $imagePath, $answer1, $answer2, $answer3, $answer4, $correct);

// Thực thi và phản hồi
if ($stmt->execute()) {
    echo "Lưu câu hỏi thành công!";
} else {
    echo "Lỗi khi lưu: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
