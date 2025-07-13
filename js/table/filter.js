function addTopicFilterToTable(table, columnIndex = 1, label = '📚 Chủ đề: ') {
  const column = table.column(columnIndex);
  const $wrapper = $('#mcTable_filter');

  // Tránh thêm lại nếu đã tồn tại
  if ($wrapper.find('select.topic-filter').length) return;

  // Tạo label và select
  const $label = $('<label style="margin-right: 10px; white-space: nowrap;">' + label + '</label>');
  const $select = $('<select class="topic-filter"><option value="">-- Tất cả --</option></select>');

  // Thêm vào DOM trước ô tìm kiếm
  $wrapper.prepend($label.append($select));

  // Thêm các giá trị duy nhất vào dropdown
  column.data().unique().sort().each(function (d) {
    const clean = $('<div>').html(d).text().trim(); // loại bỏ HTML và khoảng trắng
    if (clean !== '') {
      $select.append(`<option value="${clean}">${clean}</option>`);
    }
  });

  // Khi chọn, thực hiện lọc theo regex chính xác
  $select.on('change', function () {
    const val = $.fn.dataTable.util.escapeRegex($(this).val());
    column.search(val ? '^' + val + '$' : '', true, false).draw();
  });
}
