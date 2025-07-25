$(document).ready(function () {
    window.mcTable = $('#mcTable').DataTable({
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
  
    // Tùy biến bộ lọc chủ đề và ô tìm kiếm
    $('#mcTable_filter').html(`
      <div class="filter-left">
        📚 Chủ đề:
        <select id="filter-topic">
          <option value="">-- Tất cả --</option>
        </select>
      </div>
      <div class="filter-right">
        🔍 Tìm kiếm: <input type="search" class="form-control input-sm" placeholder="">
      </div>
    `);
  
    // Tải dữ liệu chủ đề từ PHP
    $.get('includes/mc_filter.php', function (options) {
      $('#filter-topic').append(options);
    });
  
    // Lọc theo chủ đề
    $('#filter-topic').on('change', function () {
      mcTable.column(1).search(this.value).draw();
    });
  
    // Tìm kiếm tổng
    $('#mcTable_filter input[type="search"]').on('keyup change', function () {
      mcTable.search(this.value).draw();
    });
  
    // Tìm kiếm không dấu
    $.fn.dataTable.ext.type.search.string = function (data) {
      return !data ? '' : data.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    };
  
    // Sau khi vẽ bảng
    mcTable.on('draw', function () {
      if (window.MathJax) MathJax.typesetPromise();
  
      if (!mcTable.row('.selected').node()) {
        const firstRow = mcTable.row(0);
        if (firstRow.node()) {
          $(firstRow.node()).addClass('selected');
          if (typeof sendRowData === 'function') {
            sendRowData(firstRow);
          }
        }
      }
    });
  });
  