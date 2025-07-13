function addTopicFilterToTable(table, columnIndex = 1, label = '📚') {
    table.on('init', function () {
      const column = table.column(columnIndex);
  
      const $label = $('<label style="margin-right: 10px;">' + label + '</label>');
      const $select = $('<select><option value="">-- Tất cả --</option></select>');
  
      $('#mcTable_filter').prepend($label.append($select)); // Thêm vào trước ô tìm kiếm
  
      column.data().unique().sort().each(function (d) {
        const clean = $('<div>').html(d).text();
        if (clean.trim() !== '') {
          $select.append('<option value="' + clean + '">' + clean + '</option>');
        }
      });
  
      $select.on('change', function () {
        const val = $.fn.dataTable.util.escapeRegex($(this).val());
        column.search(val ? '^' + val + '$' : '', true, false).draw();
      });
    });
  }
  