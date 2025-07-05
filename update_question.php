<?php
require 'db_connection.php';
header("Content-Type: application/json; charset=utf-8");

// ===== Lấy dữ liệu từ POST =====
function post($key) {
  return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$id         = post('id');
$topic      = post('topic');
$question   = post('question');
$answer1    = post('answer1');
$answer2    = post('answer2');
$answer3    = post('answer3');
$answer4    = post('answer4');
$correct    = post('correct_answer');
$image_url  = post('image_url');

// ===== Kiểm tra dữ liệu bắt buộc =====
if (
  !$id || !$question || !$answer1 || !$answer2 || !$answer3 || !$answer4 ||
  !in_array($correct, ['A', 'B', 'C', 'D'])
) {
  echo json_encode([
    'success' => false,
    'message' => 'Thiếu thông tin bắt buộc hoặc ID không hợp lệ'
  ]);
  exit;
}

// ===== Chuẩn bị câu lệnh SQL =====
try {
  $sql = "UPDATE questions SET 
    topic = ?, 
    question = ?, 
    answer1 = ?, 
    answer2 = ?, 
    answer3 = ?, 
    answer4 = ?, 
    correct_answer = ?, 
    image = ?
    WHERE id = ?";

  $stmt = $conn->prepare($sql);
  $success = $stmt->execute([
    $topic, $question, $answer1, $answer2, $answer3, $answer4, $correct, $image_url, $id
  ]);

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
