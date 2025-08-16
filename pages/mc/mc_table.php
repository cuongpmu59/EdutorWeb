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

<!-- Toolbar + DataTables -->
<div class="mc-toolbar">
  <div class="toolbar-left">
    <label for="importExcelInput" class="toolbar-btn">📥 Nhập Excel</label>
    <input type="file" id="importExcelInput" accept=".xlsx" hidden>
  </div>

  <div class="toolbar-right">
    <label for="filterTopic">🔍 Lọc chủ đề:</label>
    <select id="filterTopic">
      <option value="">Tất cả</option>
    </select>
  </div>
</div>

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

<script>
$(function () {
  const table = $('#mcTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '../../includes/mc/mc_fetch_data.php',
      type: 'POST',
      dataSrc: function(json) {
        console.log("👉 JSON trả về:", json);
        return json.data;
      },
      error: function(xhr) {
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
        render: data => data ? `<img src="${data}" alt="ảnh" loading="lazy">` : ''
      },
      { data: 'mc_created_at' }
    ],
    dom: '<"mc-toolbar"Bfrtip>',
    buttons: [
      {
        extend: 'excelHtml5',
        text: '📤 Xuất Excel',
        title: 'Danh sách câu hỏi',
        className: 'toolbar-btn'
      },
      {
        extend: 'print',
        text: '🖨️ In bảng',
        title: 'Danh sách câu hỏi',
        className: 'toolbar-btn'
      }
    ],
    drawCallback: function () {
      if (window.MathJax) MathJax.typesetPromise();
    },
    initComplete: function () {
      const $filter = $('#filterTopic');
      $filter.find('option:not(:first)').remove();

      $.getJSON('../../includes/mc/mc_get_topics.php')
        .done(topics => {
          if (Array.isArray(topics)) {
            topics.forEach(t => $filter.append($('<option>', { value: t, text: t })));
          }
        })
        .fail(xhr => console.error("❌ Lỗi khi tải chủ đề:", xhr.responseText));

      $filter.on('change', function () {
        const val = this.value;
        table.column(1).search(
          val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '',
          true, false
        ).draw();
      });
    }
  });

  // Import Excel
  $('#importExcelInput').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(evt) {
      try {
        const data = new Uint8Array(evt.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheetName = workbook.SheetNames[0];
        const worksheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName]);

        if (!worksheet.length) { alert('File Excel rỗng!'); return; }

        $.ajax({
          url: '../../includes/mc/mc_table_import_excel.php',
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify({ rows: worksheet }),
          success: function() {
            alert('📥 Nhập dữ liệu thành công!');
            table.ajax.reload();
          },
          error: function(err) {
            console.error("❌ Lỗi nhập Excel:", err.responseText);
            alert('❌ Lỗi khi nhập Excel');
          }
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
