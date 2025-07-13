function addTopicFilterToTable(table, columnIndex = 1, label = '📚 Chủ đề: ') {
  const column = table.column(columnIndex);
  const $filterWrapper = $('#mcTable_filter');

  // Nếu đã thêm filter thì không thêm lại
  if ($filterWrapper.find('.topic-filter').length > 0) return;

  // Tạo label và select
  const $label = $('<label>', {
    style: 'margin-right: 10px; white-space: nowrap;',
    html: label
  });
  const $select = $('<select class="topic-filter"><option value="">-- Tất cả --</option></select>');

  // Duyệt và thêm các chủ đề duy nhất
  const uniqueTopics = new Set();
  table.rows().every(function () {
    const cellData = this.data()[columnIndex];
    const text = $('<div>').html(cellData).text().trim();
    if (text) uniqueTopics.add(text);
  });

  // Đổ vào select (đã sort)
  Array.from(uniqueTopics).sort().forEach(topic => {
    $select.append($('<option>', { value: topic, text: topic }));
  });

  // Gắn vào DOM trước ô tìm kiếm
  $label.append($select);
  $filterWrapper.prepend($label);

  // Lọc khi chọn chủ đề
  $select.on('change', function () {
    const val = $.fn.dataTable.util.escapeRegex($(this).val());
    column.search(val ? '^' + val + '$' : '', true, false).draw();
  });
}
