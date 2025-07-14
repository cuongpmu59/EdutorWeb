<?php
require __DIR__ . '/../../db_connection.php';
require_once __DIR__ . '/../utils/filter.php';
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

$topics = [];
try {
  $stmtTopics = $conn->query("SELECT DISTINCT mc_topic FROM mc_questions WHERE mc_topic IS NOT NULL AND mc_topic != '' ORDER BY mc_topic");
  $topics = $stmtTopics->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📋 Câu hỏi Nhiều lựa chọn</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="../../css/main_ui.css">
  <link rel="stylesheet" href="../../css/modules/table.css">
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

<div id="imgModal" style="display:none; position:fixed;top:0;left:0;width:100%;height:100%;background:#000000bb;align-items:center;justify-content:center;z-index:1000;">
  <img id="imgModalContent" src="" style="max-width:90%;max-height:90%;border:4px solid white;box-shadow:0 0 10px white;">
</div>

<input type="file" id="excelFile" accept=".xlsx" style="display: none;">

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
      },
      {
        text: '📄 Tải mẫu Excel',
        action: function () {
          window.open('../../templates/question_template.xlsx', '_blank');
        }
      }
    ]
  });

  $('#mcTable_filter').html(`<?= getFilterHTML($topics, 'mc') ?>`);

  $('#filter-topic').on('change', function () {
    table.column(1).search(this.value).draw();
  });

  $('#mcTable_filter input[type="search"]').on('keyup change', function () {
    table.search(this.value).draw();
  });

  table.on('draw', function () {
    if (window.MathJax) MathJax.typesetPromise();
  });

  $(document).on('click', '.thumb', function () {
    $('#imgModalContent').attr('src', $(this).attr('src'));
    $('#imgModal').fadeIn();
  });
  $('#imgModal').on('click', function () {
    $(this).fadeOut();
  });

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

  $(document).on('keydown', function (e) {
    const selected = $('#mcTable tbody tr.selected');
    if (!selected.length) return;
    if (e.key === 'ArrowUp') {
      const prev = selected.prev('tr');
      if (prev.length) prev.click();
    } else if (e.key === 'ArrowDown') {
      const next = selected.next('tr');
      if (next.length) next.click();
    }
  });

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
        url: 'import_excel.php?type=mc',
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
