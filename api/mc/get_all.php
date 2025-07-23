<?php
require_once __DIR__ . '/../../includes/db_connection.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (!isset($conn)) {
  echo json_encode([]);
  exit;
}

try {
  $stmt = $conn->prepare("SELECT * FROM mc_questions ORDER BY mc_id DESC");
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($rows);
} catch (Exception $e) {
  echo json_encode([]);
}
