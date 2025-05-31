<?php
require 'db_connection.php'; // Kết nối CSDL

// Kiểm tra dữ liệu
if (!isset($_POST['id'])) {
    echo "Thiếu ID câu hỏi cần cập nhật.";
    exit;
}

$id = intval($_POST['id']);
$question = $_POST['question'] ?? '';
$answer1 = $_POST['answer1'] ?? '';
$answer2 = $_POST['answer2'] ?? '';
$answer3 = $_POST['answer3'] ?? '';
$answer4 = $_POST['answer4'] ?? '';
$correct = $_POST['correct_answer'] ?? '';
$imagePath = '';

// Xử lý ảnh nếu có
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imagePath = $targetFile;
    } else {
        echo "Lỗi khi tải ảnh lên.";
        exit;
    }
}

// Tạo truy vấn SQL
$sql = "UPDATE questions SET question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=?";
$params = [$question, $answer1, $answer2, $answer3, $answer4, $correct];

if ($imagePath !== '') {
    $sql .= ", image=?";
    $params[] = $imagePath;
}

$sql .= " WHERE id=?";
$params[] = $id;

// Chuẩn bị và thực thi truy vấn
$stmt = $conn->prepare($sql);
$result = $stmt->execute($params);

if ($result) {
    echo "Cập nhật câu hỏi thành công!";
} else {
    echo "Cập nhật thất bại.";
}
?>
