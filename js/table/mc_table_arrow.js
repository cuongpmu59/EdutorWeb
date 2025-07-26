$(document).ready(function () {
  $(document).on('keydown', function (e) {
    const selected = mcTable.row('.selected');
    if (!selected.node()) return;

    let index = selected.index();
    const total = mcTable.rows().count();

    if (e.key === 'ArrowUp') {
      index = (index - 1 + total) % total; // Quay về cuối nếu đang ở đầu
    } else if (e.key === 'ArrowDown') {
      index = (index + 1) % total; // Quay về đầu nếu đang ở cuối
    } else {
      return; // Bỏ qua nếu không phải phím mũi tên
    }

    e.preventDefault();

    // Cập nhật chọn dòng
    mcTable.$('tr.selected').removeClass('selected');
    const nextRow = mcTable.row(index);
    $(nextRow.node()).addClass('selected');

    // Cuộn vào giữa bảng
    setTimeout(() => {
      nextRow.node().scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });
    }, 10);

    // Gửi dữ liệu dòng mới về form cha
    sendRowData(nextRow);
  });
});
