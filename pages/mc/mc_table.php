<?php
require __DIR__ . '/../../db_connection.php';
if (!isset($conn)) {
  die("❌ Không thể kết nối CSDL. Kiểm tra db_connection.php");
}
header("X-Frame-Options: SAMEORIGIN");

// Lấy danh sách chủ đề
$topics = [];
try {
  $stmtTopics = $conn->query("SELECT DISTINCT mc_topic FROM mc_questions WHERE mc_topic IS NOT NULL AND mc_topic != '' ORDER BY mc_topic");
  $topics = $stmtTopics->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}

try {
  $stmt = $conn->prepare("SELECT * FROM mc_questions ORDER BY mc_id DESC");
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $rows = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📋 Câu hỏi Nhiều lựa chọn</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.dataTables.min.css">
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
    #mcTable tbody tr.selected {
      background-color: #e0f7fa !important;
    }
    .toolbar-top {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      margin-bottom: 10px;
      gap: 10px;
    }
    #imgModal {
      position: fixed;
      display: none;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.8);
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    #imgModal img {
      max-width: 90%;
      max-height: 90%;
      border: 4px solid #fff;
      box-shadow: 0 0 10px #fff;
    }

    /* ✅ Bố cục dropdown trái - search phải */
    div.dataTables_filter {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      align-items: center;
    }
    #mcTable_filter .filter-left,
    #mcTable_filter .filter-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    #mcTable_filter select {
      padding: 4px 8px;
    }
  </style>
</head>
<body>

<h2>📋 Bảng câu hỏi nhiều lựa chọn</h2>

<div class="table-wrapper">
  <table id="mcTable" class="display nowrap" style="width:100%">
    <thead>
      <tr>
        <th>ID</th><th>Chủ đề</th><th>Câu hỏi</th>
        <th>A</th><th>B</th><th>C</th><th>D</th>
        <th>Đáp án đúng</th><th>Ảnh</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $q): ?>
        <tr>
          <td><?= $q['mc_id'] ?></td>
          <td><?= htmlspecialchars($q['mc_topic']) ?></td>
          <td><?= $q['mc_question'] ?></td>
          <td><?= $q['mc_answer1'] ?></td>
          <td><?= $q['mc_answer2'] ?></td>
          <td><?= $q['mc_answer3'] ?></td>
          <td><?= $q['mc_answer4'] ?></td>
          <td><?= htmlspecialchars($q['mc_correct_answer']) ?></td>
          <td>
            <?php if (!empty($q['mc_image_url'])): ?>
              <img src="<?= htmlspecialchars($q['mc_image_url']) ?>" class="thumb" onerror="this.style.display='none'">
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Modal ảnh -->
<div id="imgModal"><img id="imgModalContent" src=""></div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>

<script>
$(document).ready(function () {
  const table = $('#mcTable').DataTable({
    scrollX: true,
    dom: 'Bfrtip',
    buttons: ['excelHtml5', 'print'],
    fixedHeader: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100]
  });

  // ✅ Tách chủ đề bên trái - tìm kiếm bên phải
  $('#mcTable_filter').html(`
    <div class="filter-left">
      📚 Lọc: 
      <select id="filter-topic">
        <option value="">-- Tất cả --</option>
        <?php foreach ($topics as $tp): echo "<option value='" . htmlspecialchars($tp) . "'>" . htmlspecialchars($tp) . "</option>"; endforeach; ?>
      </select>
    </div>
    <div class="filter-right">
      🔍 Tìm: <input type="search" class="form-control input-sm" placeholder="" aria-controls="mcTable">
    </div>
  `);

  // Tìm kiếm
  $('#mcTable_filter input[type="search"]').on('keyup change', function () {
    table.search(this.value).draw();
  });

  // Lọc chủ đề
  $('#filter-topic').on('change', function () {
    table.column(1).search(this.value).draw();
  });

  // Accent-neutralize tìm kiếm tiếng Việt
  $.fn.dataTable.ext.type.search.string = function (data) {
    return !data ? '' : data.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
  };

  // MathJax
  table.on('draw', function () {
    if (window.MathJax) MathJax.typesetPromise();
  });

  // Modal ảnh
  $(document).on('click', '.thumb', function () {
    $('#imgModalContent').attr('src', $(this).attr('src'));
    $('#imgModal').fadeIn();
  });
  $('#imgModal').on('click', function () {
    $(this).fadeOut();
  });

  // Gửi dữ liệu về form
  $('#mcTable tbody').on('click', 'tr', function () {
    const row = table.row(this).data();
    $('#mcTable tbody tr').removeClass('selected');
    $(this).addClass('selected');
    const imageSrc = $(this).find('img.thumb').attr('src') || '';
    window.parent.postMessage({
      type: 'mc_select_row',
      data: {
        id: row[0],
        topic: row[1],
        question: row[2],
        answer1: row[3],
        answer2: row[4],
        answer3: row[5],
        answer4: row[6],
        correct: row[7],
        image: imageSrc
      }
    }, '*');
  });

  // Nút thao tác
  $('#btnAddQuestion').click(() => {
    window.parent.postMessage({ type: 'mc_add_new' }, '*');
  });
  $('#btnExportExcel').click(() => $('.buttons-excel').click());
  $('#btnPrintTable').click(() => $('.buttons-print').click());
});
</script>
</body>
</html>
