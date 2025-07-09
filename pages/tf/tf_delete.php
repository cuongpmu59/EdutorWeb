<?php
require '../db_connection.php';
header('Content-Type: application/json');

// Nhận dữ liệu JSON
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu đầu vào
if (!isset($data['tf_id'])) {
  echo json_encode(['success' => false, 'message' => 'Thiếu ID câu hỏi cần xoá.']);
  exit;
}

$tf_id = intval($data['tf_id']);

try {
  $stmt = $conn->prepare("DELETE FROM tf_questions WHERE tf_id = ?");
  $stmt->bind_param("i", $tf_id);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Đã xoá câu hỏi thành công.']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy câu hỏi hoặc đã xoá trước đó.']);
  }

  $stmt->close();
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>
