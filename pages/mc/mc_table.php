// =======================
// File: pages/mc/mc_table.php
// =======================
?>
<?php
require 'db_connection.php';
$tableName = 'questions';

$rows = [];
$stmt = $conn->query("SELECT * FROM `$tableName` ORDER BY id DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$topicFilter = $_GET['topic'] ?? '';
?>
<div class="tab-content active" id="filterTab">
  <?php include '../../includes/question_filter.php'; ?>
</div>
<div class="tab-content" id="importTab">
  <?php include '../../includes/question_import.php'; ?>
</div>
<div class="tab-content" id="listTab">
  <?php include '../../includes/question_table.php'; ?>
</div>
<?php include '../../includes/question_modal.php'; ?>
<?php
