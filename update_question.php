<?php
require __DIR__ . '/../../db_connection.php';
header("Content-Type: application/json; charset=utf-8");

// ===== Hàm lấy dữ liệu POST =====
function post($key) {
  return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

// ===== Lấy dữ liệu từ POST =====
$id         = post('mc_id');
$topic      = post('mc_topic');
$question   = post('mc_question');
$answer1    = post('mc_answer1');
$answer2    = post('mc_answer2');
$answer3    = post('mc_answer3');
$answer4    = post('mc_answer4');
$correct    = post('mc_correct_answer');
$image_url  = post('mc_image_url');

// ===== Kiểm tra dữ liệu bắt buộc =====
if (
  !$id || !$question || !$answer1 || !$answer2 || !$answer3 || !$answer4 ||
  !in_array($correct, ['A', 'B', 'C', 'D'])
) {
  echo json_encode([
    'success' => false,
    'message' => 'Thiếu thông tin bắt buộc hoặc ID không hợp lệ.'
  ]);
  exit;
}

// ===== Cập nhật vào CSDL =====
try {
  $sql = "UPDATE mc_questions SET 
            mc_topic = ?, 
            mc_question = ?, 
            mc_answer1 = ?, 
            mc_answer2 = ?, 
            mc_answer3 = ?, 
            mc_answer4 = ?, 
            mc_correct_answer = ?, 
            mc_image_url = ?
          WHERE mc_id = ?";
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
