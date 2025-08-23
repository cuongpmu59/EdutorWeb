<?php
// tf_table.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý câu hỏi True/False</title>

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
<link rel="stylesheet" href="../../css/tf/tf_table_toolbar.css">
<link rel="stylesheet" href="../../css/tf/tf_table_layout.css">
</head>
<body>

<h2>📋 Danh sách câu hỏi True/False (4 statement)</h2>

<!-- Toolbar -->
<div class="tf-toolbar">
  <div class="toolbar-left">
    <label for="importExcelInput" class="toolbar-btn">📥 Nhập Excel</label>
    <input type="file" id="importExcelInput" accept=".xlsx" hidden>
    <button class="toolbar-btn" id="btnDownloadTemplate">📝 Tải Template Excel</button>
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
      <th>Statement 1</th>
      <th>Answer 1</th>
      <th>Statement 2</th>
      <th>Answer 2</th>
      <th>Statement 3</th>
      <th>Answer 3</th>
      <th>Statement 4</th>
      <th>Answer 4</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
      { data:'tf_image_url', render: d => d ? `<img src="${d}" alt="ảnh" loading="lazy">` : '' },
      { data:'tf_created_at' }
    ],
    dom: 'Brtip',
    buttons: [
      { extend:'excelHtml5', title:'Danh sách câu hỏi True/False', exportOptions:{ columns:':visible' }, className:'dt-hidden' },
      { extend:'print', title:'Danh sách câu hỏi True/False', exportOptions:{ columns:':visible' }, className:'dt-hidden' }
    ],
    responsive: true,
    scrollX: true,
    initComplete: function() {
      $.getJSON('../../includes/tf/tf_get_topics.php', function(topics){
        topics.forEach(t => $('#filterTopic').append(`<option value="${t}">${t}</option>`));
      });
    }
  });

  table.on('draw', ()=>{ if(window.MathJax) MathJax.typesetPromise(); });
  $('#filterTopic').on('change', function(){ table.column(1).search(this.value).draw(); });
  $('#customSearch').on('keyup change', function(){ table.search(this.value).draw(); });
  $('#btnExportExcel').on('click', ()=>table.button(0).trigger());
  $('#btnPrint').on('click', ()=>table.button(1).trigger());

  toastr.options = { closeButton: true, progressBar: true, positionClass: "toast-top-right", timeOut: "3000" };

  // ================== Nhập Excel ==================
  $('#importExcelInput').on('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(evt) {
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
            url: '../../includes/tf/tf_table_import_excel.php', 
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ rows: worksheet }),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    toastr.success(`📥 Nhập thành công ${res.count} dòng!`);
                    if(res.errors && res.errors.length) {
                        res.errors.forEach(e => toastr.warning(e));
                    }
                    table.ajax.reload(); 
                } else {
                    toastr.error(res.message || '❌ Lỗi khi nhập Excel');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                toastr.error('❌ Không thể gửi dữ liệu tới server');
            },
            complete: function() {
                $('#importExcelInput').val('');
            }
        });
    };
    reader.readAsArrayBuffer(file);
  });

  // ================== Tải Template Excel ==================
  $('#btnDownloadTemplate').on('click', function(){
      const header = [
        "tf_topic","tf_question",
        "tf_statement1","tf_correct_answer1",
        "tf_statement2","tf_correct_answer2",
        "tf_statement3","tf_correct_answer3",
        "tf_statement4","tf_correct_answer4",
        "tf_image_url"
      ];
      const wb = XLSX.utils.book_new();
      const ws = XLSX.utils.json_to_sheet([{}], {header: header});
      XLSX.utils.book_append_sheet(wb, ws, "Template");
      XLSX.writeFile(wb, "template_tf_questions.xlsx");
  });
});
</script>
</script>
<!-- Hỗ trợ các sự kiện -->
<script src="../../js/tf/tf_table_event.js"></script>
</body>
</html>
