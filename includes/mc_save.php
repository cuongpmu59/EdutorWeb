<?php
require_once __DIR__ . '/db_connection.php';

header('Content-Type: application/json');

try {
  $mc_id = $_POST['mc_id'] ?? null;
  $topic = $_POST['mc_topic'] ?? '';
  $question = $_POST['mc_question'] ?? '';
  $a1 = $_POST['mc_answer1'] ?? '';
  $a2 = $_POST['mc_answer2'] ?? '';
  $a3 = $_POST['mc_answer3'] ?? '';
  $a4 = $_POST['mc_answer4'] ?? '';
  $correct = $_POST['mc_correct_answer'] ?? '';
  $image_url = $_POST['mc_image_url'] ?? null;

  if (!$topic || !$question || !$a1 || !$a2 || !$a3 || !$a4 || !$correct) {
    throw new Exception('Vui lòng nhập đầy đủ thông tin.');
  }

  if ($mc_id) {
    // Cập nhật
    $stmt = $conn->prepare("UPDATE mc_questions SET mc_topic=?, mc_question=?, mc_answer1=?, mc_answer2=?, mc_answer3=?, mc_answer4=?, mc_correct_answer=?, mc_image_url=? WHERE mc_id=?");
    $stmt->execute([$topic, $question, $a1, $a2, $a3, $a4, $correct, $image_url, $mc_id]);
  } else {
    // Thêm mới
    $stmt = $conn->prepare("INSERT INTO mc_questions (mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4, mc_correct_answer, mc_image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$topic, $question, $a1, $a2, $a3, $a4, $correct, $image_url]);
    $mc_id = $conn->lastInsertId();
  }

  echo json_encode(['success' => true, 'mc_id' => $mc_id]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
