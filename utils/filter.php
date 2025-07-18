<?php
require_once __DIR__ . '/../db_connection.php';
$stmt = $conn->query("SELECT DISTINCT mc_topic FROM mc_questions WHERE mc_topic IS NOT NULL AND mc_topic != '' ORDER BY mc_topic");
?>
<select id="filter-topic">
  <option value="">-- Tất cả --</option>
  <?php foreach ($stmt as $row): ?>
    <option value="<?= htmlspecialchars($row['mc_topic']) ?>">
      <?= htmlspecialchars($row['mc_topic']) ?>
    </option>
  <?php endforeach; ?>
</select>
