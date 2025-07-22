$(document).ready(function () {
    $(document).on('keydown', function (e) {
      const selected = mcTable.row('.selected');
      if (!selected.node()) return;
  
      let index = selected.index();
      const total = mcTable.rows().count();
  
      if (e.key === 'ArrowUp') {
        index = (index - 1 + total) % total;
      } else if (e.key === 'ArrowDown') {
        index = (index + 1) % total;
      } else {
        return;
      }
  
      e.preventDefault();
  
      // Xoá selected trước khi thêm mới
      mcTable.$('tr.selected').removeClass('selected');
  
      // Dùng API DataTables để truy xuất dòng mới
      const nextRow = mcTable.row(index);
  
      // Chỉ thêm class mà không fadeIn/hide gì cả
      $(nextRow.node()).addClass('selected');
  
      // Đảm bảo dòng đã có trong DOM, rồi cuộn
      setTimeout(() => {
        nextRow.node().scrollIntoView({ behavior: 'smooth', block: 'center' });
      }, 10); // Cho DOM kịp render lại
  
      sendRowData(nextRow);
    });
  });
  