<?php
require __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../utils/filter.php';
if (!isset($conn)) {
  die("❌ Không thể kết nối CSDL. Kiểm tra db_connection.php");
}
header("X-Frame-Options: SAMEORIGIN");

try {
  $stmt = $conn->prepare("SELECT * FROM tf_questions ORDER BY tf_id DESC");
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $rows = [];
}

// Lấy danh sách chủ đề
$topics = [];
try {
  $stmtTopics = $conn->query("SELECT DISTINCT tf_topic FROM tf_questions WHERE tf_topic IS NOT NULL AND tf_topic != '' ORDER BY tf_topic");
  $topics = $stmtTopics->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📋 Câu hỏi Đúng/Sai</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="../../css/main_ui.css">
  <link rel="stylesheet" href="../../css/modules/table.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <style>
    .thumb {
      max-width: 50px;
      max-height: 50px;
      cursor: pointer;
    }
    #tfTable tbody tr.selected {
      background-color: #e0f7fa !important;
    }
  </style>
</head>
<body>

<h2>📋 Bảng câu hỏi Đúng/Sai</h2>

<div class="table-wrapper">
  <table id="tfTable" class="display nowrap" style="width:100%">
    <thead>
      <tr>
        <th>ID</th><th>Chủ đề</th><th>Câu hỏi</th><th>Ảnh</th>
        <th>Mệnh đề 1</th><th>Đúng 1</th>
        <th>Mệnh đề 2</th><th>Đúng 2</th>
        <th>Mệnh đề 3</th><th>Đúng 3</th>
        <th>Mệnh đề 4</th><th>Đúng 4</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $q): ?>
        <tr>
          <td><?= $q['tf_id'] ?></td>
          <td><?= htmlspecialchars($q['tf_topic']) ?></td>
          <td><?= $q['tf_question'] ?></td>
          <td>
            <?php if (!empty($q['tf_image_url'])): ?>
              <img src="<?= htmlspecialchars($q['tf_image_url']) ?>" class="thumb" onerror="this.style.display='none'">
            <?php endif; ?>
          </td>
          <td><?= $q['tf_statement1'] ?></td><td><?= $q['tf_correct_answer1'] ?></td>
          <td><?= $q['tf_statement2'] ?></td><td><?= $q['tf_correct_answer2'] ?></td>
          <td><?= $q['tf_statement3'] ?></td><td><?= $q['tf_correct_answer3'] ?></td>
          <td><?= $q['tf_statement4'] ?></td><td><?= $q['tf_correct_answer4'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function () {
  const table = $('#tfTable').DataTable({
    scrollX: true,
    dom: '<"top-controls"Bf>rtip',
    fixedHeader: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    buttons: [
      {
        extend: 'excelHtml5',
        text: '⬇️ Xuất Excel',
        title: 'tf_questions',
        exportOptions: { columns: ':visible' }
      },
      {
        extend: 'print',
        text: '🖨️ In bảng',
        exportOptions: { columns: ':visible' }
      }
    ]
  });

  // Bộ lọc chủ đề
  $('#tfTable_filter').html(`<?= getFilterHTML($topics, 'tf') ?>`);
  $('#filter-topic').on('change', function () {
    table.column(1).search(this.value).draw();
  });
  $('#tfTable_filter input[type="search"]').on('keyup change', function () {
    table.search(this.value).draw();
  });

  // MathJax re-render
  table.on('draw', function () {
    if (window.MathJax) MathJax.typesetPromise();
  });
});
</script>

</body>
</html>
