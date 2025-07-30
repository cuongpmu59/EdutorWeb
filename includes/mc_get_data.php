<?php
// includes/mc_get_data.php

require_once __DIR__ . '/../includes/db_connection.php';
header('Content-Type: application/json; charset=utf-8');

try {
  $stmt = $conn->query("
    SELECT 
      mc_id, mc_topic, mc_question, 
      mc_answer1, mc_answer2, mc_answer3, mc_answer4, 
      mc_correct_answer, mc_image_url
    FROM mc_questions
    ORDER BY mc_id DESC
  ");
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
  echo json_encode([
    'data' => [],
    'error' => $e->getMessage()
  ], JSON_UNESCAPED_UNICODE);
}
