<?php
require '../db_connection.php'; // Đảm bảo file này kết nối đúng CSDL
header('Content-Type: application/json');

// Nhận dữ liệu JSON từ client
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu
if (!$data || empty($data['tf_topic']) || empty($data['tf_question'])) {
  echo json_encode(['success' => false, 'message' => 'Thiếu chủ đề hoặc đề bài.']);
  exit;
}

try {
  // Chuẩn bị truy vấn
  $stmt = $conn->prepare("
    INSERT INTO tf_questions (
      tf_topic, tf_question,
      tf_statement1, tf_correct_answer1,
      tf_statement2, tf_correct_answer2,
      tf_statement3, tf_correct_answer3,
      tf_correct_answer4, tf_image_url
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

  $stmt->bind_param(
    "ssssssssss",
    $data['tf_topic'],
    $data['tf_question'],
    $data['tf_statement1'],
    $data['tf_correct_answer1'],
    $data['tf_statement2'],
    $data['tf_correct_answer2'],
    $data['tf_statement3'],
    $data['tf_correct_answer3'],
    $data['tf_correct_answer4'],
    $data['tf_image_url']
  );

  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    echo json_encode([
      'success' => true,
      'message' => 'Đã thêm câu hỏi thành công.',
      'inserted_id' => $stmt->insert_id
    ]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Không thể thêm câu hỏi.']);
  }

  $stmt->close();
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>
