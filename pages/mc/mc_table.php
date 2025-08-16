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

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<!-- Custom CSS -->
<link rel="stylesheet" href="../../css/mc/mc_table_toolbar.css">
<link rel="stylesheet" href="../../css/mc/mc_table_layout.css">

<style>
  /* Giới hạn kích thước ảnh */
  #mcTable img {
    max-width: 80px;
    max-height: 80px;
    border-radius: 6px;
  }
</style>
</head>
<body>

<h2>📋 Danh sách câu hỏi trắc nghiệm</h2>

<!-- Toolbar -->
<div class="mc-toolbar">
  <div class="toolbar-left">
    <label for="importExcelInput" class="toolbar-btn">📥 Nhập Excel</label>
    <input type="file" id="importExcelInput" accept=".xlsx" hidden>

    <button class="toolbar-btn" data-action="export">📤 Xuất Excel</button>
    <button class="toolbar-btn" data-action="print">🖨️ In bảng</button>
  </div>

  <div class="toolbar-right">
    <label for="filterTopic">🔍 Lọc chủ đề:</label>
    <select id="filterTopic">
      <option value="">Tất cả</option>
    </select>
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
  // Khởi tạo DataTable
  const table = $('#mcTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '../../includes/mc/mc_fetch_data.php',
      type: 'POST',
      error: function (xhr) {
        console.error("❌ Lỗi Ajax:", xhr.responseText);
      }
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
        render: data => data ? `<img src="${data}" alt="ảnh">` : ''
      }
    ],
    dom: 'Bfrtip',
    buttons: [
      { extend: 'excelHtml5', title: 'Danh sách câu hỏi', className: 'd-none' },
      { extend: 'print', title: 'Danh sách câu hỏi', className: 'd-none' }
    ],
    drawCallback: function () {
      // Render lại MathJax khi bảng cập nhật
      if (window.MathJax) MathJax.typeset();
    },
    // Lấy danh sách chủ đề từ DB
    initComplete: function () {
    const $filter = $('#filterTopic');

    $filter.find('option:not(:first)').remove();
    $.getJSON('../../includes/mc/mc_get_topics.php')
    .done(function (topics) {
      if (Array.isArray(topics) && topics.length) {
        topics.forEach(t => $filter.append(`<option value="${t}">${t}</option>`));
      } else {
        console.warn("⚠️ Không có chủ đề nào trong DB");
      }
    })
    .fail(function (xhr) {
      console.error("❌ Lỗi khi tải chủ đề:", xhr.responseText);
    });

  // Gán sự kiện lọc (xóa cũ rồi gán mới để tránh double-bind)
    $filter.off('change').on('change', function () {
    table.column(1).search(this.value).draw();
    });
  }
  // Nút Export / Print
  $('[data-action="export"]').on('click', () => table.button(0).trigger());
  $('[data-action="print"]').on('click', () => table.button(1).trigger());

  // Import Excel (chuyển sang file js riêng nếu muốn)
  $('#importExcelInput').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (evt) {
      try {
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
            console.error("❌ Lỗi nhập Excel:", err.responseText);
            alert('❌ Lỗi khi nhập Excel');
          });
      } catch (ex) {
        console.error(ex);
        alert('❌ File Excel không hợp lệ');
      }
    };
    reader.readAsArrayBuffer(file);
  });
});
</script>

<!-- Điều khiển bằng phím mũi tên -->
<script src="../../js/mc/mc_table_arrow_key.js"></script>
<script src="../../js/mc/mc_table_import_excel.js"></script>

</body>
</html>
