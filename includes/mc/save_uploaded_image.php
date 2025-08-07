<?php
require_once __DIR__ . '/../../includes/db_connection.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

try {
  // Kiểm tra dữ liệu đầu vào
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);
    $image_url = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL);

    if (!$mc_id || !$image_url) {
      echo json_encode(['error' => '❌ Thiếu mc_id hoặc image_url']);
      exit;
    }

    // Cập nhật vào bảng mc_questions
    $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url = :image_url WHERE id = :mc_id");
    $stmt->bindParam(':image_url', $image_url);
    $stmt->bindParam(':mc_id', $mc_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
      echo json_encode(['success' => true]);
    } else {
      echo json_encode(['error' => '❌ Không thể cập nhật CSDL']);
    }
  } else {
    echo json_encode(['error' => '❌ Yêu cầu không hợp lệ']);
  }

} catch (Exception $e) {
  echo json_encode(['error' => '❌ Lỗi máy chủ: ' . $e->getMessage()]);
}
