<?php
require 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? '';
$image_url = $data['image_url'] ?? '';

if (!$id || !$image_url) {
  echo json_encode(["success" => false, "message" => "Thiếu ID hoặc URL ảnh."]);
  exit;
}

try {
  $stmt = $conn->prepare("UPDATE questions SET image_url = ? WHERE id = ?");
  $stmt->execute([$image_url, $id]);
  echo json_encode(["success" => true]);
} catch (Exception $e) {
  echo json_encode(["success" => false, "message" => "Lỗi DB: " . $e->getMessage()]);
}
