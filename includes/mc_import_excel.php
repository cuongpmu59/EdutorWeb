<?php
require_once __DIR__ . '/../../includes/db_connection.php';
header('Content-Type: application/json');

if (!isset($conn)) {
  http_response_code(500);
  echo json_encode(['error' => '❌ Không thể kết nối CSDL.']);
  exit;
}

// Nhận dữ liệu JSON
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!is_array($data) || count($data) === 0) {
  http_response_code(400);
  echo json_encode(['error' => '❌ Dữ liệu không hợp lệ.']);
  exit;
}

$inserted = 0;
$returnData = [];

try {
  $stmt = $conn->prepare("INSERT INTO mc_questions 
    (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

  foreach ($data as $row) {
    $topic   = trim($row['mc_topic'] ?? '');
    $question = trim($row['mc_question'] ?? '');
    $a1      = trim($row['mc_answer1'] ?? '');
    $a2      = trim($row['mc_answer2'] ?? '');
    $a3      = trim($row['mc_answer3'] ?? '');
    $a4      = trim($row['mc_answer4'] ?? '');
    $correct = strtoupper(trim($row['mc_correct_answer'] ?? ''));
    $image   = trim($row['mc_image_url'] ?? '');

    if (!$topic || !$question || !$a1 || !$a2 || !$a3 || !$a4 || !in_array($correct, ['A','B','C','D'])) {
      continue;
    }

    $stmt->execute([$topic, $question, $a1, $a2, $a3, $a4, $correct, $image]);

    $mc_id = $conn->lastInsertId(); // ✅ Lấy ID vừa thêm

    $returnData[] = [
      'mc_id' => $mc_id,
      'mc_topic' => $topic,
      'mc_question' => $question,
      'mc_answer1' => $a1,
      'mc_answer2' => $a2,
      'mc_answer3' => $a3,
      'mc_answer4' => $a4,
      'mc_correct_answer' => $correct,
      'mc_image_url' => $image
    ];

    $inserted++;
  }

  echo json_encode(['inserted' => $inserted, 'data' => $returnData]);

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => '❌ Lỗi khi chèn dữ liệu: ' . $e->getMessage()]);
}
