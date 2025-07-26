function sendRowData(row) {
  const $cells = $(row.node()).children('td');
  const mc_id = $cells.eq(0).data('raw');

  if (mc_id) {
    window.parent.postMessage({
      type: 'mc_select_row',
      data: { mc_id: mc_id }
    }, '*');
  }
}

$('#mcTable tbody').on('click', 'tr', function () {
  $('#mcTable tbody tr').removeClass('selected');
  $(this).addClass('selected');

  const row = mcTable.row(this);
  sendRowData(row);
});
