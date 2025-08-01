<?php
// includes/mc/mc_get_data.php

require_once __DIR__ . '/../../includes/db_connection.php';
header('Content-Type: application/json');

try {
  // Nếu có POST mc_id → trả về 1 bản ghi cụ thể
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_id'])) {
    $mc_id = intval($_POST['mc_id']);

    $stmt = $conn->prepare("
      SELECT mc_id, mc_topic, mc_question, 
             mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
             mc_correct_answer, mc_image_url
      FROM mc_questions
      WHERE mc_id = :mc_id
      LIMIT 1
    ");
    $stmt->execute(['mc_id' => $mc_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
      echo json_encode($data);
    } else {
      echo json_encode(['error' => '❌ Không tìm thấy dữ liệu']);
    }
    exit;
  }

  // Nếu không có POST mc_id → trả về danh sách toàn bộ
  $stmt = $conn->query("
    SELECT 
      mc_id, mc_topic, mc_question, 
      mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
      mc_correct_answer, mc_image_url
    FROM mc_questions
    ORDER BY mc_id DESC
  ");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode(['data' => $rows]);
} catch (PDOException $e) {
  echo json_encode([
    'data' => [],
    'error' => $e->getMessage()
  ]);
}
