<?php
require 'db_connection.php';
echo "Kết nối thành công!";
// Cho phép phản hồi JSON
header('Content-Type: application/json');

// Kiểm tra phương thức POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ']);
    exit;
}

// Lấy dữ liệu từ POST
$question = trim($_POST['question'] ?? '');
$answer1 = trim($_POST['answer1'] ?? '');
$answer2 = trim($_POST['answer2'] ?? '');
$answer3 = trim($_POST['answer3'] ?? '');
$answer4 = trim($_POST['answer4'] ?? '');
$correct_answer = strtoupper(trim($_POST['correct_answer'] ?? ''));

// Kiểm tra dữ liệu bắt buộc
if ($question === '' || $answer1 === '' || $answer2 === '' || $answer3 === '' || $answer4 === '' || $correct_answer === '') {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin']);
    exit;
}

// Xử lý upload ảnh (nếu có)
$imageName = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/images/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // tạo thư mục nếu chưa có
    }

    $originalName = basename($_FILES['image']['name']);
    $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName); // tên an toàn
    $uploadFile = $uploadDir . $imageName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        echo json_encode(['status' => 'error', 'message' => 'Không thể lưu ảnh.']);
        exit;
    }
}

try {
    $sql = "INSERT INTO questions (question, answer1, answer2, answer3, answer4, correct_answer, image)
            VALUES (:question, :answer1, :answer2, :answer3, :answer4, :correct_answer, :image)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':question', $question);
    $stmt->bindParam(':answer1', $answer1);
    $stmt->bindParam(':answer2', $answer2);
    $stmt->bindParam(':answer3', $answer3);
    $stmt->bindParam(':answer4', $answer4);
    $stmt->bindParam(':correct_answer', $correct_answer);
    $stmt->bindParam(':image', $imageName);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Thêm câu hỏi thành công']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể thêm câu hỏi']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi: ' . $e->getMessage()]);
}
