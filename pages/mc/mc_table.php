<?php
// mc_table.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý câu hỏi</title>

<!-- MathJax -->
<script>
window.MathJax = {
  tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
  svg: { fontCache: 'global' }
};
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>

<!-- DataTables + Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="../../css/mc/mc_table_toolbar.css">
<link rel="stylesheet" href="../../css/mc/mc_table_layout.css">

</head>
<body>

<h2>📋 Danh sách câu hỏi trắc nghiệm</h2>

<!-- Toolbar -->
<div class="mc-toolbar">
  <div class="toolbar-left">
    <label for="importExcelInput" class="toolbar-btn">📥 Nhập Excel</label>
    <input type="file" id="importExcelInput" accept=".xlsx" hidden>

    <button class="toolbar-btn" id="btnExportExcel">📤 Xuất Excel</button>
    <button class="toolbar-btn" id="btnPrint">🖨️ In bảng</button>
  </div>

  <div class="toolbar-right">
    <label for="filterTopic">🔍 Lọc chủ đề:</label>
    <select id="filterTopic">
      <option value="">Tất cả</option>
    </select>

    <label for="filterDate">📅 Lọc ngày tạo:</label>
    <input type="date" id="filterDate">
  </div>
</div>

<!-- DataTable -->
<table id="mcTable" class="display nowrap" style="width:100%">
  <thead>
    <tr>
      <th>ID</th>
      <th>Chủ đề</th>
      <th>Câu hỏi</th>
      <th>A</th>
      <th>B</th>
      <th>C</th>
      <th>D</th>
      <th>Đáp án</th>
      <th>Hình minh họa</th>
      <th>Ngày tạo</th>
    </tr>
  </thead>
</table>

<!-- jQuery + DataTables + Buttons + SheetJS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
$(function () {
  const table = $('#mcTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '../../includes/mc/mc_fetch_data.php',
      type: 'POST'
    },
    order: [[0, 'desc']],
    columns: [
      { data: 'mc_id' },
      { data: 'mc_topic' },
      { data: 'mc_question' },
      { data: 'mc_answer1' },
      { data: 'mc_answer2' },
      { data: 'mc_answer3' },
      { data: 'mc_answer4' },
      { data: 'mc_correct_answer' },
      {
        data: 'mc_image_url',
        render: function (data) {
          return data ? `<img src="${data}" alt="ảnh" style="max-width:80px;">` : '';
        }
      },
      { data: 'mc_created_at' }
    ],
    dom: 'Bfrtip',
    buttons: [
      { extend: 'excelHtml5', title: 'Danh sách câu hỏi', exportOptions: { columns: ':visible' } },
      { extend: 'print', title: 'Danh sách câu hỏi', exportOptions: { columns: ':visible' } }
    ],
    responsive: true,
    scrollX: true,
    initComplete: function () {
      // Lấy danh sách chủ đề từ DB
      $.getJSON('../../includes/mc/mc_get_topics.php', function (topics) {
        topics.forEach(t => {
          $('#filterTopic').append(`<option value="${t}">${t}</option>`);
        });
      });
    }
  });

  // Lọc theo chủ đề
  $('#filterTopic').on('change', function () {
    table.column(1).search(this.value).draw();
  });

  // Lọc theo ngày tạo
  $('#filterDate').on('change', function () {
    table.column(9).search(this.value).draw();
  });

  // Export Excel
  $('#btnExportExcel').on('click', function () {
    table.button(0).trigger();
  });

  // Print
  $('#btnPrint').on('click', function () {
    table.button(1).trigger();
  });

  // Import Excel
  $('#importExcelInput').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (evt) {
      const data = new Uint8Array(evt.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const sheetName = workbook.SheetNames[0];
      const worksheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName]);

      if (worksheet.length === 0) {
        alert('File Excel rỗng!');
        return;
      }

      $.post('../../includes/mc/mc_table_import_excel.php', { rows: JSON.stringify(worksheet) })
        .done(res => {
          alert('📥 Nhập dữ liệu thành công!');
          table.ajax.reload();
        })
        .fail(err => {
          console.error(err);
          alert('❌ Lỗi khi nhập Excel');
        });
    };
    reader.readAsArrayBuffer(file);
  });
});
</script>

<!-- Optional: điều khiển bằng phím mũi tên -->
<script src="../../js/mc/mc_table_arrow_key.js"></script>

</body>
</html>
