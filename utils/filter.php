<?php
require_once __DIR__ . '/../db_connection.php';

try {
  $stmt = $conn->query("SELECT DISTINCT mc_topic FROM mc_questions WHERE mc_topic IS NOT NULL AND mc_topic != '' ORDER BY mc_topic");
  $topics = $stmt->fetchAll(PDO::FETCH_COLUMN);
  foreach ($topics as $tp) {
    echo "<option value='" . htmlspecialchars($tp, ENT_QUOTES) . "'>" . htmlspecialchars($tp) . "</option>";
  }
} catch (Exception $e) {
  echo "<option disabled>Lỗi khi tải chủ đề</option>";
}
?>
