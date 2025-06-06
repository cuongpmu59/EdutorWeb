<?php
// Kết nối MySQL
$host = sql103.infinityfree.com;
$db = if0_39011369_questionBank;
$user = if0_39011369; // thay bằng user thực tế
$pass = Kimdung61;     // thay bằng mật khẩu nếu có
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nhận dữ liệu từ form
$question = $_POST['question'];
$correct = $_POST['correct_answer'];
$answers = [
    'answer1' => $_POST['answer1'],
    'answer2' => $_POST['answer2'],
    'answer3' => $_POST['answer3'] ?? null,
    'answer4' => $_POST['answer4'] ?? null
];

// Xử lý upload ảnh
$image_path = null;
if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'images/images_questionBank';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    $filename = time() . '_' . basename($_FILES['image']['name']);
    $target_path = $upload_dir . $filename;
    move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
    $image_path = $target_path;
}

// Lưu vào bảng questions
$stmt = $conn->prepare("INSERT INTO questions (question_text, image_path, correct_answer) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $question, $image_path, $correct);
$stmt->execute();
$question_id = $stmt->insert_id;
$stmt->close();

// Lưu vào bảng answers
foreach ($answers as $key => $text) {
    if (!empty($text)) {
        $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_key, answer_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $question_id, $key, $text);
        $stmt->execute();
        $stmt->close();
    }
}

file_put_contents('questions.txt', json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    echo "✅ Câu hỏi đã được lưu thành công!";
} else {
    echo "❌ Không nhận được dữ liệu từ form.";
}

$conn->close();
echo "Đã lưu câu hỏi thành công.";
?>
