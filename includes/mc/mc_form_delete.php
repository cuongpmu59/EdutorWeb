<?php
require_once __DIR__ . '/../includes/db_connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
  $mc_id = intval($_POST['mc_id']);

  try {
    $stmt = $conn->prepare("DELETE FROM mc_questions WHERE mc_id = ?");
    $stmt->execute([$mc_id]);

    if ($stmt->rowCount() > 0) {
      echo json_encode(['success' => true, 'message' => 'Xoá thành công']);
    } else {
      echo json_encode(['success' => false, 'message' => 'Không tìm thấy câu hỏi để xoá.']);
    }
  } catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
}
