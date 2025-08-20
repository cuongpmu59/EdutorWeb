<?php
// sa_table.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý câu hỏi SA</title>

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

<!-- Custom CSS -->
<link rel="stylesheet" href="../../css/sa/sa_table_toolbar.css">
<link rel="stylesheet" href="../../css/sa/sa_table_layout.css">

<style>
#saTable td.sa-question-cell {
  max-width: 300px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
#saTable img {
  max-width: 80px;
  height: auto;
}
.dataTables_filter { display: none; }
.dt-hidden { display: none; }
</style>
</head>
<body>

<h2>📋 Danh sách câu hỏi Short Answer</h2>

<!-- Toolbar -->
<div class="sa-toolbar">
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
<table id="saTable" class="display nowrap" style="width:100%">
  <thead>
    <tr>
      <th>ID</th>
      <th>Chủ đề</th>
      <th>Câu hỏi</th>
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

<script>
$(function () {
  const table = $('#saTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: { url: '../../includes/sa/sa_fetch_data.php', type: 'POST' },
    order: [[0,'desc']],
    stateSave: true,
    columns: [
      { data:'sa_id' },
      { data:'sa_topic' },
      { data:'sa_question', className:'sa-question-cell',
        render: d => d ? `<span title="${d.replace(/"/g,'&quot;')}">
                            ${d.length>80 ? d.substr(0,80)+'…' : d}
                          </span>` : ''
      },
      { data:'sa_correct_answer' },
      { data:'sa_image_url', render: d => d ? `<img src="${d}" alt="ảnh" loading="lazy">` : '' },
      { data:'sa_created_at' }
    ],
    dom: 'Brtip',
    buttons: [
      { extend:'excelHtml5', title:'Danh sách câu hỏi SA', exportOptions:{ columns:':visible' }, className:'dt-hidden' },
      { extend:'print', title:'Danh sách câu hỏi SA', exportOptions:{ columns:':visible' }, className:'dt-hidden' }
    ],
    responsive: true,
    scrollX: true,
    initComplete: function() {
      $.getJSON('../../includes/sa/sa_get_topics.php', function(topics){
        topics.forEach(t => $('#filterTopic').append(`<option value="${t}">${t}</option>`));
      });
    }
  });

  table.on('draw', ()=>{ if(window.MathJax) MathJax.typesetPromise(); });

  $('#filterTopic').on('change', function(){ table.column(1).search(this.value).draw(); });
  $('#customSearch').on('keyup change', function(){ table.search(this.value).draw(); });

  $('#btnExportExcel').on('click', ()=>table.button(0).trigger());
  $('#btnPrint').on('click', ()=>table.button(1).trigger());

  $('#importExcelInput').on('change', function(e){
    const file = e.target.files[0];
    if(!file) return;
    const reader = new FileReader();
    reader.onload = function(evt){
      const data = new Uint8Array(evt.target.result);
      const workbook = XLSX.read(data,{type:'array'});
      const sheetName = workbook.SheetNames[0];
      const worksheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName],{defval:''});
      if(!worksheet.length){ alert('File Excel rỗng!'); return; }
      $.post('../../includes/sa/sa_table_import_excel.php',{rows:JSON.stringify(worksheet)})
       .done(()=>{ alert('📥 Nhập dữ liệu thành công!'); table.ajax.reload(); })
       .fail(()=>{ alert('❌ Lỗi khi nhập Excel'); });
    };
    reader.readAsArrayBuffer(file);
  });
});
</script>

<script src="../../js/sa/sa_table_arrow_key.js"></script>

</body>
</html>
