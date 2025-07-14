<?php
require __DIR__ . '/../../db_connection.php';
if (!isset($conn)) {
  die("❌ Không thể kết nối CSDL. Kiểm tra db_connection.php");
}
header("X-Frame-Options: SAMEORIGIN");

// Danh sách chủ đề
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
  <link rel="stylesheet" href="../../css/main_ui.css">
  <style>
    .table-toolbar {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }
    .toolbar-left select {
      padding: 6px 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background: #fff;
    }
    .toolbar-right .btn {
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s ease;
    }
    .btn.blue { background-color: #2196F3; }
    .btn.green { background-color: #4CAF50; }
    .btn.gray { background-color: #9E9E9E; }
    .btn:hover { opacity: 0.85; }
    .thumb { max-width: 50px; max-height: 50px; cursor: pointer; }
    #imgModal {
      display: none; position: fixed; top: 0; left: 0;
      width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8);
      align-items: center; justify-content: center; z-index: 1000;
    }
    #imgModal img {
      max-width: 90%; max-height: 90%; border: 4px solid white;
      box-shadow: 0 0 10px white;
    }
  </style>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>

<h2>📋 Bảng câu hỏi nhiều lựa chọn</h2>

<div class="table-toolbar">
  <div class="toolbar-left">
    📚 Chủ đề:
    <select id="filter-topic">
      <option value="">-- Tất cả --</option>
      <?php foreach ($topics as $tp): ?>
        <option value="<?= htmlspecialchars($tp) ?>"><?= htmlspecialchars($tp) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="toolbar-right">
    <button class="btn blue" id="btnImportExcel">📅 Nhập Excel</button>
    <button class="btn green" id="btnExportExcel">⬇️ Xuất Excel</button>
    <button class="btn gray" id="btnPrintTable">🖨️ In bảng</button>
    <input type="file" id="excelFile" accept=".xlsx" style="display: none;">
  </div>
</div>

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
          <td><?php if (!empty($q['mc_image_url'])): ?><img src="<?= htmlspecialchars($q['mc_image_url']) ?>" class="thumb"><?php endif; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div id="imgModal"><img id="imgModalContent" src=""></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function () {
  const table = $('#mcTable').DataTable({
    scrollX: true,
    dom: 'Bfrtip',
    buttons: [
      { extend: 'excelHtml5', className: 'd-none', title: 'mc_questions' },
      { extend: 'print', className: 'd-none' }
    ]
  });

  $('#filter-topic').on('change', function () {
    table.column(1).search(this.value).draw();
  });

  $('#btnExportExcel').click(() => table.button('.buttons-excel').trigger());
  $('#btnPrintTable').click(() => table.button('.buttons-print').trigger());
  $('#btnImportExcel').click(() => $('#excelFile').click());

  $('#excelFile').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const sheet = workbook.Sheets[workbook.SheetNames[0]];
      const jsonData = XLSX.utils.sheet_to_json(sheet, { defval: '' });
      if (jsonData.length === 0) return alert("❌ File rỗng hoặc lỗi.");
      $.ajax({
        url: 'import_mc_excel.php', method: 'POST', contentType: 'application/json',
        data: JSON.stringify(jsonData),
        success: res => { alert(`✅ Đã nhập ${res.inserted} câu!`); location.reload(); },
        error: () => alert("❌ Lỗi khi nhập.")
      });
    };
    reader.readAsArrayBuffer(file);
  });

  // Modal Ảnh
  $(document).on('click', '.thumb', function () {
    $('#imgModalContent').attr('src', $(this).attr('src'));
    $('#imgModal').fadeIn();
  });
  $('#imgModal').on('click', function () {
    $(this).fadeOut();
  });
});
</script>
</body>
</html>
