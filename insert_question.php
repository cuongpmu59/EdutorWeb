<?php
require 'db_connection.php';
header("Content-Type: application/json; charset=utf-8");

// ===== Hàm lấy dữ liệu từ POST =====
function post($key) {
  return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

// ===== Nhận dữ liệu =====
$topic     = post('topic');
$question  = post('question');
$answer1   = post('answer1');
$answer2   = post('answer2');
$answer3   = post('answer3');
$answer4   = post('answer4');
$correct   = post('correct_answer');
$image_url = post('image_url');

// ===== Kiểm tra dữ liệu bắt buộc =====
if (
  !$question || !$answer1 || !$answer2 || !$answer3 || !$answer4 ||
  !in_array($correct, ['A', 'B', 'C', 'D'])
) {
  echo json_encode([
    'success' => false,
    'message' => 'Thiếu thông tin bắt buộc'
  ]);
  exit;
}

// ===== Thêm vào CSDL với bắt lỗi =====
try {
  $sql = "INSERT INTO questions (question, image, answer1, answer2, answer3, answer4, correct_answer, topic)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $success = $stmt->execute([
    $question,
    $image_url,
    $answer1,
    $answer2,
    $answer3,
    $answer4,
    $correct,
    $topic
  ]);

  $id = $success ? $conn->lastInsertId() : null;

  echo json_encode([
    'success' => $success,
    'id' => $id
  ]);

} catch (PDOException $e) {
  echo json_encode([
    'success' => false,
    'message' => 'Lỗi SQL: ' . $e->getMessage()
  ]);
}
