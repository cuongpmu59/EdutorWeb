<?php
require_once __DIR__ . '/../db_connection.php';

$topicFilter = $_GET['topic'] ?? '';
$table = $_GET['table'] ?? 'questions'; // mặc định cho MC
$topics = [];

try {
    $stmt = $conn->prepare("SELECT DISTINCT topic FROM $table WHERE topic IS NOT NULL AND topic != '' ORDER BY topic");
    $stmt->execute();
    $topics = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $topics = [];
}
?>

<div id="filterTab" class="tab-content active">
  <label><strong>Lọc theo chủ đề:</strong></label>
  <select id="filterTopicInline">
    <option value="">-- Tất cả --</option>
    <?php foreach ($topics as $t): ?>
      <option value="<?= htmlspecialchars($t) ?>" <?= $topicFilter === $t ? 'selected' : '' ?>>
        <?= htmlspecialchars($t) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>
