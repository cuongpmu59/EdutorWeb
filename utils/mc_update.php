<?php
require_once 'db_connection.php';
header("Content-Type: application/json; charset=utf-8");

// ===== Hàm lấy dữ liệu POST =====
function post($key) {
  return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

// ===== Nhận dữ liệu =====
$id         = post('mc_id');
$topic      = post('mc_topic');
$question   = post('mc_question');
$answer1    = post('mc_answer1');
$answer2    = post('mc_answer2');
$answer3    = post('mc_answer3');
$answer4    = post('mc_answer4');
$correct    = post('mc_correct_answer');

// ===== Kiểm tra dữ liệu bắt buộc =====
if (
  !$id || !$topic || !$question || !$answer1 || !$answer2 ||
  !$answer3 || !$answer4 || !in_array($correct, ['A', 'B', 'C', 'D'])
) {
  echo json_encode([
    'success' => false,
    'message' => 'Thiếu thông tin hoặc ID không hợp lệ.'
  ]);
  exit;
}

// ===== Xử lý ảnh (nếu có) =====
$image_url = '';
if (isset($_FILES['mc_image']) && $_FILES['mc_image']['error'] === UPLOAD_ERR_OK) {
  $ext = pathinfo($_FILES['mc_image']['name'], PATHINFO_EXTENSION);
  $image_name = 'mc_img_' . $id . '.' . $ext;
  $target_path = 'uploads/' . $image_name;

  // Tạo thư mục nếu chưa có
  if (!is_dir('uploads')) mkdir('uploads', 0777, true);

  if (move_uploaded_file($_FILES['mc_image']['tmp_name'], $target_path)) {
    $image_url = $target_path;
  }
} else {
  // Nếu không có ảnh mới thì giữ nguyên ảnh cũ
  $stmt = $conn->prepare("SELECT image FROM questions WHERE id = ?");
  $stmt->execute([$id]);
  $image_url = $stmt->fetchColumn();
}

// ===== Cập nhật câu hỏi =====
try {
  $sql = "UPDATE questions SET 
    mc_topic = ?, 
    mc_question = ?, 
    mc_answer1 = ?, 
    mc_answer2 = ?, 
    mc_answer3 = ?, 
    mc_answer4 = ?, 
    mc_correct_answer = ?, 
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
