// filter.js - Thêm bộ lọc chủ đề vào ô tìm kiếm DataTables
function addTopicFilterToTable(table, topicColumnIndex = 1, label = '📚') {
    table.on('init', function () {
      const column = table.column(topicColumnIndex);
      const $label = $('<label>' + label + ' </label>');
      const $select = $('<select><option value="">-- Tất cả --</option></select>');
  
      // Thêm vào ô tìm kiếm
      $('#mcTable_filter').prepend($label.append($select));
  
      // Khi chọn chủ đề thì lọc cột tương ứng
      $select.on('change', function () {
        const val = $.fn.dataTable.util.escapeRegex($(this).val());
        column.search(val ? '^' + val + '$' : '', true, false).draw();
      });
  
      // Lấy giá trị duy nhất từ cột dữ liệu
      column.data().unique().sort().each(function (d) {
        const cleanText = $('<div>').html(d).text(); // Loại bỏ HTML
        if (cleanText.trim() !== '') {
          $select.append('<option value="' + cleanText + '">' + cleanText + '</option>');
        }
      });
    });
  }
  