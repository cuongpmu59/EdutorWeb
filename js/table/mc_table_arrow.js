$(document).on('keydown', function (e) {
  if (!window.mcTable) return;

  const table = mcTable;
  const rows = table.rows({ page: 'current' });
  const total = rows.count();

  if (!['ArrowUp', 'ArrowDown'].includes(e.key)) return;

  e.preventDefault();

  const selected = table.row('.selected');
  let index = selected.index();

  // Nếu chưa chọn dòng nào, mặc định chọn dòng đầu tiên
  if (index === undefined || index === null || index < 0) {
    index = 0;
  } else {
    if (e.key === 'ArrowUp') {
      index = (index - 1 + total) % total;
    } else if (e.key === 'ArrowDown') {
      index = (index + 1) % total;
    }
  }

  // Cập nhật highlight dòng
  table.$('tr.selected').removeClass('selected');
  const nextRow = table.row(index);
  $(nextRow.node()).addClass('selected');

  // Cuộn dòng được chọn vào giữa khung bảng
  nextRow.node().scrollIntoView({
    behavior: 'smooth',
    block: 'center'
  });

  // Gửi dữ liệu dòng đang chọn về form cha
  if (typeof sendRowData === 'function') {
    sendRowData(nextRow);
  }
});
