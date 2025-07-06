<?php
require 'db_connection.php';
header("Content-Type: text/html; charset=utf-8");

// Hàm lấy và làm sạch dữ liệu POST
function post($key) {
    return trim($_POST[$key] ?? '');
}

// ===== Lấy dữ liệu =====
$topic = post('topic');
$main_question = post('main_question');

$statement1 = post('statement1');
$correct1   = intval($_POST['correct_answer1'] ?? 0);

$statement2 = post('statement2');
$correct2   = intval($_POST['correct_answer2'] ?? 0);

$statement3 = post('statement3');
$correct3   = intval($_POST['correct_answer3'] ?? 0);

$statement4 = post('statement4');
$correct4   = intval($_POST['correct_answer4'] ?? 0);

$image = post('image_url'); // hidden input lưu URL ảnh minh hoạ từ Cloudinary

// ===== Kiểm tra hợp lệ =====
if (!$topic || !$main_question || !$statement1 || !$statement2 || !$statement3 || !$statement4) {
    exit("❌ Thiếu thông tin. Vui lòng nhập đầy đủ.");
}

// ===== Chuẩn bị câu truy vấn =====
$stmt = $conn->prepare("
  INSERT INTO true_false_questions (
    topic, main_question,
    statement1, answer1, correct_answer1,
    statement2, answer2, correct_answer2,
    statement3, answer3, correct_answer3,
    statement4, answer4, correct_answer4,
    image
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$null1 = $null2 = $null3 = $null4 = NULL;

$stmt->bind_param(
  "sssisiisiisiis",
  $topic, $main_question,
  $statement1, $null1, $correct1,
  $statement2, $null2, $correct2,
  $statement3, $null3, $correct3,
  $statement4, $null4, $correct4,
  $image
);

// ===== Thực thi =====
if ($stmt->execute()) {
    echo "<script>alert('✅ Đã lưu câu hỏi thành công!'); window.location.href='true_false_question_form.php';</script>";
} else {
    echo "❌ Lỗi khi lưu: " . $stmt->error;
}
?>
