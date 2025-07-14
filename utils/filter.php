<?php
require_once __DIR__ . '/../db_connection.php';

$topics = [];
try {
  $stmtTopics = $conn->query("SELECT DISTINCT mc_topic FROM mc_questions WHERE mc_topic IS NOT NULL AND mc_topic != '' ORDER BY mc_topic");
  $topics = $stmtTopics->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
  $topics = [];
}

// Xuất HTML các option để nhúng vào select
foreach ($topics as $tp) {
  echo "<option value='" . htmlspecialchars($tp) . "'>" . htmlspecialchars($tp) . "</option>";
}
?>
