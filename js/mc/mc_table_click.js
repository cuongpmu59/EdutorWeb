// mc_table_click.js
$(document).ready(function () {
    const table = $('#mcTable').DataTable();
    let selectedRowIndex = null;
  
    // Bắt sự kiện click vào 1 hàng
    $('#mcTable tbody').on('click', 'tr', function () {
      const rowIndex = table.row(this).index();
      if (rowIndex === undefined) return;
  
      // Bỏ highlight cũ
      $('#mcTable tbody tr').removeClass('selected');
      // Thêm highlight cho hàng hiện tại
      $(this).addClass('selected');
      selectedRowIndex = rowIndex;
  
      // Lấy dữ liệu của dòng đó
      const rowData = table.row(rowIndex).data();
      if (!rowData) return;
  
      // Gửi dữ liệu sang form cha
      window.parent.postMessage(
        {
          type: 'fill-form',
          data: rowData
        },
        '*'
      );
    });
  });
  