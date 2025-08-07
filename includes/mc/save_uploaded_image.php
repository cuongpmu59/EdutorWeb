<?php
require_once __DIR__ . '/../../includes/db_connection.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => '❌ Yêu cầu phải là POST']);
    exit;
  }

  $mc_id = filter_input(INPUT_POST, 'mc_id', FILTER_VALIDATE_INT);
  $image_url = filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL);

  if (!$mc_id || !$image_url) {
    echo json_encode([
      'error' => '❌ Thiếu mc_id hoặc image_url',
      'mc_id' => $mc_id,
      'image_url' => $image_url
    ]);
    exit;
  }

  $stmt = $conn->prepare("UPDATE mc_questions SET mc_image_url = ? WHERE mc_id = ?");
  $stmt->execute([$image_url, $mc_id]);

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['error' => '❌ Lỗi máy chủ: ' . $e->getMessage()]);
}
