<?php
require __DIR__ . 'db_connection.php';
require __DIR__ . 'dotenv.php';
require __DIR__ . 'includes/question_data.php';
$rows = getQuestions($conn, 'true_false_questions');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách câu hỏi Đúng/Sai</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="css/table_view.css">
</head>
<body>

<div class="tab-container">
  <button class="tab-button active" data-tab="filterTab">🔍 Bộ lọc</button>
  <button class="tab-button" data-tab="importTab">📁 Nhập / Xuất</button>
  <button class="tab-button" data-tab="listTab">📄 Danh sách</button>
</div>

<?php include 'includes/question_filter.php'; ?>
<?php include 'includes/question_import.php'; ?>
<?php include 'includes/question_table.php'; ?>
<?php include 'includes/question_modal.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script src="js/table/table.js"></script>
</body>
</html>
