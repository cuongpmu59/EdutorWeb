// Gửi mc_id của dòng được chọn về form cha
function sendRowData(row) {
  const $cells = $(row.node()).children('td');
  const mc_id = $cells.eq(0).data('raw'); // Lấy mc_id từ data-raw trong ô đầu tiên

  if (mc_id) {
    window.parent.postMessage({
      type: 'mc_select_row',
      data: { mc_id: mc_id }
    }, '*');
  }
}

// Xử lý sự kiện khi click vào một dòng trong bảng
$('#mcTable tbody').on('click', 'tr', function () {
  $('#mcTable tbody tr').removeClass('selected'); // Bỏ chọn các dòng khác
  $(this).addClass('selected');                   // Đánh dấu dòng hiện tại

  const row = mcTable.row(this);                  // Lấy đối tượng row từ DataTable
  sendRowData(row);                               // Gửi dữ liệu về form cha
});
