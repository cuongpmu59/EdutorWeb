<?php
// tf_table.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý câu hỏi Đúng/Sai</title>

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
<link rel="stylesheet" href="../../css/tf/tf_table_toolbar.css">
<link rel="stylesheet" href="../../css/tf/tf_table_layout.css">

<style>
#tfTable td.tf-question-cell {
  max-width: 300px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
#tfTable img {
  max-width: 80px;
  height: auto;
}
.dataTables_filter { display: none; } /* Ẩn search mặc định */
.dt-hidden { display: none; }         /* Ẩn nút gốc, chỉ trigger thủ công */
</style>
</head>
<body>

<h2>📋 Danh sách câu hỏi Đúng/Sai</h2>

<!-- Toolbar -->
<div class="tf-toolbar">
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
<table id="tfTable" class="display nowrap" style="width:100%">
  <thead>
    <tr>
      <th>ID</th>
      <th>Chủ đề</th>
      <th>Câu hỏi</th>
      <th>Mệnh đề 1</th>
      <th>Đáp án 1</th>
      <th>Mệnh đề 2</th>
      <th>Đáp án 2</th>
      <th>Mệnh đề 3</th>
      <th>Đáp án 3</th>
      <th>Mệnh đề 4</th>
      <th>Đáp án 4</th>
      <th>Hình minh họa</th>
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
  const table = $('#tfTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: { url: '../../includes/tf/tf_fetch_data.php', type: 'POST' },
    order: [[0,'desc']],
    stateSave: true,
    columns: [
      { data:'tf_id' },
      { data:'tf_topic' },
      { data:'tf_question', className:'tf-question-cell',
        render: d => d ? `<span title="${d.replace(/"/g,'&quot;')}">
                            ${d.length>80 ? d.substr(0,80)+'…' : d}
                          </span>` : ''
      },
      { data:'tf_statement1' },
      { data:'tf_correct_answer1' },
      { data:'tf_statement2' },
      { data:'tf_correct_answer2' },
      { data:'tf_statement3' },
      { data:'tf_correct_answer3' },
      { data:'tf_statement4' },
      { data:'tf_correct_answer4' },
      { data:'tf_image_url', render: d => d ? `<img src="${d}" alt="ảnh" loading="lazy">` : '' }
    ],
    dom: 'Brtip',
    buttons: [
      { extend:'excelHtml5', title:'Danh sách câu hỏi Đúng/Sai', exportOptions:{ columns:':visible' }, className:'dt-hidden' },
      { extend:'print', title:'Danh sách câu hỏi Đúng/Sai', exportOptions:{ columns:':visible' }, className:'dt-hidden' }
    ],
    responsive: true,
    scrollX: true,
    initComplete: function() {
      $.getJSON('../../includes/tf/tf_get_topics.php', function(topics){
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

  // Nhập Excel
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
      $.post('../../includes/tf/tf_table_import_excel.php',{rows:JSON.stringify(worksheet)})
       .done(()=>{ alert('📥 Nhập dữ liệu thành công!'); table.ajax.reload(); })
       .fail(()=>{ alert('❌ Lỗi khi nhập Excel'); });
    };
    reader.readAsArrayBuffer(file);
  });
});
</script>

<!-- Hỗ trợ di chuyển bằng phím -->
<script src="../../js/tf/tf_table_arrow_key.js"></script>

</body>
</html>
