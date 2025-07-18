<?php
require __DIR__ . '/../../db_connection.php';
if (!isset($conn)) {
  die("❌ Không thể kết nối CSDL. Kiểm tra db_connection.php");
}
header("X-Frame-Options: SAMEORIGIN");

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
  <link rel="stylesheet" href="../../css/table/table.css">
  
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <style>
    .thumb {
      max-width: 50px;
      max-height: 50px;
      cursor: pointer;
    }
    #mcTable tbody tr.selected {
      background-color: #e0f7fa !important;
    }
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
    #excelFile {
      display: none;
    }
  </style>
</head>

<body>
<div id="custom-filter-bar" class="custom-filter" style="margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
  <div class="filter-left">
    📚 Chủ đề:
    <select id="filter-topic">
      <option value="">-- Tất cả --</option>
      <?php include __DIR__ . '/utils/filter.php'; ?>
    </select>
  </div>
  <div class="filter-right">
    🔍 Tìm kiếm: <input type="search" id="custom-search" class="form-control input-sm" placeholder="">
  </div>
</div>

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
          <td data-raw="<?= $q['mc_id'] ?>"><?= $q['mc_id'] ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_topic']) ?>"><?= htmlspecialchars($q['mc_topic']) ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_question']) ?>"><?= $q['mc_question'] ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_answer1']) ?>"><?= $q['mc_answer1'] ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_answer2']) ?>"><?= $q['mc_answer2'] ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_answer3']) ?>"><?= $q['mc_answer3'] ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_answer4']) ?>"><?= $q['mc_answer4'] ?></td>
          <td data-raw="<?= htmlspecialchars($q['mc_correct_answer']) ?>"><?= htmlspecialchars($q['mc_correct_answer']) ?></td>
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
<div id="imgModal" style="display:none; position:fixed;top:0;left:0;width:100%;height:100%;background:#000000bb;align-items:center;justify-content:center;z-index:1000;">
  <img id="imgModalContent" src="" style="max-width:90%;max-height:90%;border:4px solid white;box-shadow:0 0 10px white;">
</div>

<input type="file" id="excelFile" accept=".xlsx" />

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
    dom: '<"top-controls"Bf>rtip',
    fixedHeader: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    buttons: [
      {
        extend: 'excelHtml5',
        text: '⬇️ Xuất Excel',
        title: 'mc_questions',
        exportOptions: { columns: ':visible' }
      },
      {
        extend: 'print',
        text: '🖨️ In bảng',
        exportOptions: { columns: ':visible' }
      },
      {
        text: '📥 Nhập Excel',
        action: function () { $('#excelFile').click(); }
      }
    ]
  });

  // Lọc theo chủ đề
  $('#filter-topic').on('change', function () {
  table.column(1).search(this.value).draw();
  });

  // Tìm kiếm chung
  $('#custom-search').on('keyup change', function () {
  table.search(this.value).draw();
  });


  $('#filter-topic').on('change', function () {
    table.column(1).search(this.value).draw();
  });

  $('#mcTable_filter input[type="search"]').on('keyup change', function () {
    table.search(this.value).draw();
  });

  $.fn.dataTable.ext.type.search.string = function (data) {
    return !data ? '' : data.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
  };

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

  // 📤 Gửi dữ liệu gốc về form cha
  function sendRowData(row) {
    const $cells = $(row.node()).find('td');
    const getRaw = i => $cells.eq(i).data('raw') || '';

    const data = {
      id: getRaw(0),
      topic: getRaw(1),
      question: getRaw(2),
      answer1: getRaw(3),
      answer2: getRaw(4),
      answer3: getRaw(5),
      answer4: getRaw(6),
      correct: getRaw(7),
      image: $cells.eq(8).find('img.thumb').attr('src') || ''
    };

    window.parent.postMessage({ type: 'mc_select_row', data }, '*');
    if (window.MathJax) MathJax.typesetPromise(); // render lại form nếu cần
  }

  // Click chọn dòng
  $('#mcTable tbody').on('click', 'tr', function () {
    table.$('tr.selected').removeClass('selected');
    $(this).addClass('selected');
    sendRowData(table.row(this));
  });

  // Mũi tên lên/xuống để điều hướng
  $(document).on('keydown', function (e) {
    const selected = table.row('.selected');
    if (!selected.node()) return;
    let index = selected.index();
    if (e.key === 'ArrowUp' && index > 0) index--;
    else if (e.key === 'ArrowDown' && index < table.rows().count() - 1) index++;
    else return;

    e.preventDefault();
    table.$('tr.selected').removeClass('selected');
    const nextRow = table.row(index);
    $(nextRow.node()).addClass('selected')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    sendRowData(nextRow);
  });

  // Nhập Excel
  $('#excelFile').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const worksheet = workbook.Sheets[workbook.SheetNames[0]];
      const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: '' });

      if (jsonData.length === 0) {
        alert("❌ File Excel rỗng hoặc không hợp lệ.");
        return;
      }

      $.ajax({
        url: 'import_mc_excel.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(jsonData),
        success: function (res) {
          alert("✅ Đã nhập " + res.inserted + " câu hỏi!");
          location.reload();
        },
        error: function () {
          alert("❌ Lỗi khi nhập file Excel.");
        }
      });
    };
    reader.readAsArrayBuffer(file);
  });
});
</script>
</body>
</html>
