let mcTable;

$(document).ready(function () {
  mcTable = $('#mcTable').DataTable({
    scrollX: true,
    scrollY: '500px',
    paging: false, // Không phân trang — cuộn mượt
    fixedHeader: true,
    dom: '<"top-controls"Bf>rtip',
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
        action: function () {
          $('#excelFile').click();
        }
      }
    ],
    initComplete: function () {
      // Chọn dòng đầu tiên sau khi khởi tạo
      const firstRow = mcTable.row(0);
      if (firstRow.node()) {
        $(firstRow.node()).addClass('selected');
        setTimeout(() => {
          firstRow.node().scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 30);
        if (typeof sendRowData === 'function') sendRowData(firstRow);
      }
    }
  });

  // Giao diện lọc + tìm kiếm
  initComplete: function () {
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

  // Nạp danh sách chủ đề
  $.get('includes/mc_filter.php', function (options) {
    $('#filter-topic').append(options);
  });

  // Lọc theo chủ đề
  $('#filter-topic').on('change', function () {
    mcTable.column(1).search(this.value).draw();
  });
}
  // Tìm kiếm tổng (toàn bảng)
  $('#mcTable_filter input[type="search"]').on('keyup change', function () {
    mcTable.search(this.value).draw();
  });

  // Tìm kiếm không dấu
  $.fn.dataTable.ext.type.search.string = function (data) {
    return !data ? '' : data.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
  };

  // Sau khi vẽ lại bảng (VD: sau khi lọc)
  mcTable.on('draw', function () {
    if (window.MathJax) MathJax.typesetPromise();

    // Nếu chưa có dòng nào được chọn → chọn dòng đầu
    if (!mcTable.row('.selected').node()) {
      const firstRow = mcTable.row(0);
      if (firstRow.node()) {
        $(firstRow.node()).addClass('selected');
        setTimeout(() => {
          firstRow.node().scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 30);
        if (typeof sendRowData === 'function') sendRowData(firstRow);
      }
    }
  });
});
