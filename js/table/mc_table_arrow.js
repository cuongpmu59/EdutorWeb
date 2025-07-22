$(document).ready(function () {
    $(document).on('keydown', function (e) {
      const selected = mcTable.row('.selected');
      if (!selected.node()) return;
  
      let index = selected.index();
      const total = mcTable.rows().count();
  
      if (e.key === 'ArrowUp') {
        index = (index - 1 + total) % total; // Về dòng cuối nếu ở dòng đầu
      } else if (e.key === 'ArrowDown') {
        index = (index + 1) % total; // Về dòng đầu nếu ở dòng cuối
      } else {
        return;
      }
  
      e.preventDefault();
  
      // Cập nhật dòng được chọn
      mcTable.$('tr.selected').removeClass('selected');
      const nextRow = mcTable.row(index);
  
      $(nextRow.node()).addClass('selected');
  
      // Cuộn dòng vào giữa màn hình
      setTimeout(() => {
        nextRow.node().scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });
      }, 10);
  
      sendRowData(nextRow);
    });
  });
  