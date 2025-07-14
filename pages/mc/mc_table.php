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

$topicFilter = $_GET['topic'] ?? '';
try {
  $sql = $topicFilter !== ''
    ? "SELECT * FROM mc_questions WHERE mc_topic = :topic ORDER BY mc_id DESC"
    : "SELECT * FROM mc_questions ORDER BY mc_id DESC";
  $stmt = $conn->prepare($sql);
  $topicFilter !== '' ? $stmt->execute(['topic' => $topicFilter]) : $stmt->execute();
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
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.dataTables.min.css" />
  <link rel="stylesheet" href="../../css/modules/table.css">
  <link rel="stylesheet" href="../../css/main_ui.css">

  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async
    src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <style>
    .thumb {
      max-width: 50px;
      max-height: 50px;
      cursor: pointer;
    }
    #mcTable tbody tr.selected {
      background-color: #e0f7fa !important;
    }
  </style>
</head>
<body>

<h2>📋 Bảng câu hỏi nhiều lựa chọn</h2>

<div class="toolbar">
  <div class="left-tools">
    <label for="topicSelect">📚 Chủ đề:</label>
    <select id="topicSelect">
      <option value="">-- Tất cả --</option>
      <?php foreach ($topics as $t): ?>
        <option value="<?= htmlspecialchars($t) ?>" <?= ($t === $topicFilter) ? 'selected' : '' ?>>
          <?= htmlspecialchars($t) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <button id="btnAddQuestion">➕ Thêm câu hỏi</button>
    <button id="btnReloadTable">🔄 Làm mới</button>
  </div>
  <div class="right-tools">
    <button id="btnImportExcel">📁 Nhập Excel</button>
    <button id="btnExportExcel">⬇️ Xuất Excel</button>
    <button id="btnPrintTable">🖨️ In bảng</button>
  </div>
</div>

<input type="file" id="excelInput" accept=".xlsx,.xls" style="display:none;">

<div class="table-wrapper">
  <table id="mcTable" class="display" style="width:100%">
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

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>

<script src="../../js/table/button.js"></script>
<script src="../../js/table/excel_io.js"></script>
<script src="../../js/table/image_modal.js"></script>
<script src="../../js/table/transfer_table.js"></script>
<script src="../../js/table/filter.js"></script>

<script>
  $(document).ready(function () {
    const table = $('#mcTable').DataTable({
      dom: 'Bfrtip',
      buttons: ['excelHtml5', 'print'],
      fixedHeader: true,
      pageLength: 10,
      drawCallback: function () {
        if (window.MathJax) MathJax.typeset();
      },
      language: {
        search: "🔍 Tìm kiếm:",
        lengthMenu: "Hiển thị _MENU_ dòng",
        info: "Trang _PAGE_ / _PAGES_ (_TOTAL_ dòng)",
        infoEmpty: "Không có dữ liệu",
        zeroRecords: "Không tìm thấy kết quả phù hợp",
        paginate: {
          first: "«", last: "»", next: "▶", previous: "◀"
        },
      }
    });

    // Khi chọn chủ đề → lọc server
    $('#topicSelect').on('change', function () {
      const topic = $(this).val();
      const url = new URL(window.location.href);
      if (topic) url.searchParams.set('topic', topic);
      else url.searchParams.delete('topic');
      window.location.href = url.toString(); // Tải lại trang
    });

    // Gửi dữ liệu về form cha khi chọn dòng
    $('#mcTable tbody').on('click', 'tr', function () {
      $('#mcTable tbody tr').removeClass('selected');
      $(this).addClass('selected');
      const rowData = table.row(this).data();
      if (!rowData || window.parent === window) return;
      const imageSrc = $('<div>').html(rowData[8]).find('img').attr('src') || '';
      window.parent.postMessage({
        type: 'mc_selected_row',
        data: {
          mc_id: rowData[0],
          mc_topic: rowData[1],
          mc_question: rowData[2],
          mc_answer1: rowData[3],
          mc_answer2: rowData[4],
          mc_answer3: rowData[5],
          mc_answer4: rowData[6],
          mc_correct_answer: rowData[7],
          mc_image_url: imageSrc
        }
      }, '*');
    });
  });
</script>
</body>
</html>
