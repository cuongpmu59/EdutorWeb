<?php
require __DIR__ . '/../../db_connection.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data) || count($data) === 0) {
  http_response_code(400);
  echo json_encode(["error" => "❌ Dữ liệu không hợp lệ."]);
  exit;
}

$inserted = 0;

foreach ($data as $row) {
  $type = strtoupper(trim($row['type'] ?? ''));
  $topic = $row['topic'] ?? '';
  $question = $row['question'] ?? '';
  $image = $row['image_url'] ?? '';

  try {
    switch ($type) {
      case 'MC':
        $a1 = $row['answer1'] ?? '';
        $a2 = $row['answer2'] ?? '';
        $a3 = $row['answer3'] ?? '';
        $a4 = $row['answer4'] ?? '';
        $correct = $row['correct'] ?? '';

        $stmt = $conn->prepare("
          INSERT INTO mc_questions (
            mc_topic, mc_question,
            mc_answer1, mc_answer2, mc_answer3, mc_answer4,
            mc_correct_answer, mc_image_url
          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$topic, $question, $a1, $a2, $a3, $a4, $correct, $image]);
        $inserted++;
        break;

      case 'TF':
        $s1 = $row['statement1'] ?? '';
        $c1 = isset($row['correct1']) ? (int)$row['correct1'] : null;
        $s2 = $row['statement2'] ?? '';
        $c2 = isset($row['correct2']) ? (int)$row['correct2'] : null;
        $s3 = $row['statement3'] ?? '';
        $c3 = isset($row['correct3']) ? (int)$row['correct3'] : null;
        $s4 = $row['statement4'] ?? '';
        $c4 = isset($row['correct4']) ? (int)$row['correct4'] : null;

        $stmt = $conn->prepare("
          INSERT INTO tf_questions (
            tf_topic, tf_question,
            tf_statement1, tf_correct_answer1,
            tf_statement2, tf_correct_answer2,
            tf_statement3, tf_correct_answer3,
            tf_statement4, tf_correct_answer4,
            tf_image_url
          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
          $topic, $question,
          $s1, $c1,
          $s2, $c2,
          $s3, $c3,
          $s4, $c4,
          $image
        ]);
        $inserted++;
        break;

      case 'SA':
        $correct = $row['correct'] ?? '';
        $stmt = $conn->prepare("
          INSERT INTO sa_questions (
            sa_topic, sa_question, sa_correct_answer, sa_image_url
          ) VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$topic, $question, $correct, $image]);
        $inserted++;
        break;

      default:
        // Bỏ qua nếu type không hợp lệ
        continue;
    }

  } catch (Exception $e) {
    // Có thể log lỗi nếu muốn
    continue;
  }
}

echo json_encode(["inserted" => $inserted]);
