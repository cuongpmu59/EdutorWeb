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

<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- Custom CSS -->
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
    <select id="filterTopic"><option value="">Tất cả</option></select>

    <label for="customSearch">🔎 Tìm kiếm:</label>
    <input type="text" id="customSearch" placeholder="Nhập từ khóa...">
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

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(function () {
  const table = $('#mcTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: { url: '../../includes/mc/mc_fetch_data.php', type: 'POST' },
    order: [[0,'desc']],
    stateSave: true,
    columns: [
      { data:'mc_id' },
      { data:'mc_topic' },
      { data:'mc_question', className:'mc-question-cell',
        render: d => d ? `<span title="${d.replace(/"/g,'&quot;')}">
                            ${d.length>80 ? d.substr(0,80)+'…' : d}
                          </span>` : ''
      },
      { data:'mc_answer1' },
      { data:'mc_answer2' },
      { data:'mc_answer3' },
      { data:'mc_answer4' },
      { data:'mc_correct_answer' },
      { data:'mc_image_url', render: d => d ? `<img src="${d}" alt="ảnh" loading="lazy">` : '' },
      { data:'mc_created_at' }
    ],
    dom: 'Brtip',
    buttons: [
      { extend:'excelHtml5', title:'Danh sách câu hỏi', exportOptions:{ columns:':visible' }, className:'dt-hidden' },
      { extend:'print', title:'Danh sách câu hỏi', exportOptions:{ columns:':visible' }, className:'dt-hidden' }
    ],
    responsive: true,
    scrollX: true,
    initComplete: function() {
      $.getJSON('../../includes/mc/mc_get_topics.php', function(topics){
        topics.forEach(t => $('#filterTopic').append(`<option value="${t}">${t}</option>`));
      });
    }
  });

  // MathJax render lại mỗi khi vẽ bảng
  table.on('draw', ()=>{ if(window.MathJax) MathJax.typesetPromise(); });

  // Filter + Search thủ công
  $('#filterTopic').on('change', function(){ table.column(1).search(this.value).draw(); });
  $('#customSearch').on('keyup change', function(){ table.search(this.value).draw(); });

  // Xuất Excel + Print trigger thủ công
  $('#btnExportExcel').on('click', ()=>table.button(0).trigger());
  $('#btnPrint').on('click', ()=>table.button(1).trigger());

  // Toastr config mặc định
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: "3000"
  };

  // Nhập Excel
  // Nhập Excel
$('#importExcelInput').on('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(evt) {
        try {
            const data = new Uint8Array(evt.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const sheetName = workbook.SheetNames[0];
            const worksheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], { defval: '' });

            if (!worksheet.length) {
                toastr.warning('📂 File Excel rỗng!');
                $('#importExcelInput').val('');
                return;
            }

            toastr.info('⏳ Đang nhập dữ liệu, vui lòng chờ...');

            $.ajax({
                url: '../../includes/mc/mc_table_import_excel.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ rows: worksheet }), // quan trọng: phải có key 'rows'
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        toastr.success(`📥 Nhập thành công ${res.count} dòng!`);
                        table.ajax.reload();
                    } else {
                        toastr.error(res.message || '❌ Lỗi khi nhập Excel');
                    }
                },
                error: function() {
                    toastr.error('❌ Không thể gửi dữ liệu tới server');
                },
                complete: function() {
                    $('#importExcelInput').val('');
                }
            });
        } catch (err) {
            console.error(err);
            toastr.error('❌ Không thể đọc file Excel');
            $('#importExcelInput').val('');
        }
    };
    reader.readAsArrayBuffer(file);
});

});
</script>

<!-- Hỗ trợ di chuyển bằng phím -->
<script src="../../js/mc/mc_table_arrow_key.js"></script>

</body>
</html>
