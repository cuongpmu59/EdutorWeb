<?php
require '../db_connection.php';
header('Content-Type: application/json');

// Nhận dữ liệu từ yêu cầu
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu cơ bản
if (!$data || empty($data['tf_id']) || empty($data['tf_topic']) || empty($data['tf_question'])) {
  echo json_encode(['success' => false, 'message' => 'Thiếu ID, chủ đề hoặc đề bài.']);
  exit;
}

try {
  $stmt = $conn->prepare("
    UPDATE tf_questions SET
      tf_topic = ?, tf_question = ?,
      tf_statement1 = ?, tf_correct_answer1 = ?,
      tf_statement2 = ?, tf_correct_answer2 = ?,
      tf_statement3 = ?, tf_correct_answer3 = ?,
      tf_correct_answer4 = ?, tf_image_url = ?
    WHERE tf_id = ?
  ");

  $stmt->bind_param(
    "ssssssssssi",
    $data['tf_topic'],
    $data['tf_question'],
    $data['tf_statement1'],
    $data['tf_correct_answer1'],
    $data['tf_statement2'],
    $data['tf_correct_answer2'],
    $data['tf_statement3'],
    $data['tf_correct_answer3'],
    $data['tf_correct_answer4'],
    $data['tf_image_url'],
    $data['tf_id']
  );

  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Đã cập nhật thành công.']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Không có thay đổi hoặc ID không tồn tại.']);
  }

  $stmt->close();
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>
