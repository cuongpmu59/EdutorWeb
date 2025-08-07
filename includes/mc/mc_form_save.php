<?php
require_once __DIR__ . '/../includes/db_connection.php';
header('Content-Type: application/json');

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Phương thức không hợp lệ.');
  }

  // Lấy dữ liệu từ POST
  $mc_id = isset($_POST['mc_id']) && trim($_POST['mc_id']) !== '' ? intval($_POST['mc_id']) : null;

  // Các trường có thể gửi
  $fields = ['mc_topic', 'mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer', 'mc_image_url', 'public_id'];
  $data = [];

  foreach ($fields as $field) {
    if (isset($_POST[$field]) && trim($_POST[$field]) !== '') {
      $data[$field] = trim($_POST[$field]);
    }
  }

  if ($mc_id) {
    // ✅ CẬP NHẬT — chỉ update các trường được gửi
    if (empty($data)) {
      throw new Exception('Không có dữ liệu nào để cập nhật.');
    }

    $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
    $data['mc_id'] = $mc_id;

    $stmt = $conn->prepare("UPDATE mc_questions SET $setClause WHERE mc_id = :mc_id");
    $stmt->execute($data);

    echo json_encode(['success' => true, 'updated' => $stmt->rowCount()]);

  } else {
    // ✅ THÊM MỚI — yêu cầu các trường chính, KHÔNG bắt buộc ảnh
    $required = ['mc_topic', 'mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer'];
    foreach ($required as $field) {
      if (empty($data[$field])) {
        throw new Exception("Thiếu trường bắt buộc: $field");
      }
    }

    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));

    $stmt = $conn->prepare("INSERT INTO mc_questions ($columns) VALUES ($placeholders)");
    $stmt->execute($data);

    echo json_encode(['success' => true, 'inserted_id' => $conn->lastInsertId()]);
  }

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
