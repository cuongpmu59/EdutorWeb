<?php
require_once __DIR__ . '/../env/dotenv.php'; 
require_once __DIR__ . '/db_connection.php';

header('Content-Type: application/json');

if (!isset($_GET['mc_id']) || !is_numeric($_GET['mc_id'])) {
  echo json_encode(['error' => 'Thiếu hoặc sai mã câu hỏi']);
  exit;
}

$mc_id = intval($_GET['mc_id']);

try {
  $stmt = $conn->prepare("SELECT * FROM mc_questions WHERE mc_id = ?");
  $stmt->execute([$mc_id]);
  $question = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($question) {
    echo json_encode(['data' => $question]); // ✅ Bọc vào 'data'
  } else {
    echo json_encode(['error' => 'Không tìm thấy câu hỏi']);
  }
} catch (Exception $e) {
  echo json_encode(['error' => 'Lỗi truy vấn']);
}
