// js/tf/tf_table_arrow_key.js
let selectedRowIndex = null;

$(document).ready(function () {
  const table = $('#tfTable').DataTable();

  // Reset highlight khi redraw
  table.on('draw', function () {
    selectedRowIndex = null;
    $('#tfTable tbody tr').removeClass('selected');
  });

  // --- Xử lý phím lên/xuống/enter ---
  $(document).on('keydown', function (e) {
    const rows = table.rows({ search: 'applied' }).nodes();
    if (!rows.length) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      selectedRowIndex = (selectedRowIndex === null || selectedRowIndex >= rows.length - 1)
        ? 0
        : selectedRowIndex + 1;
      highlightAndSend(rows, selectedRowIndex, table);
    }

    if (e.key === 'ArrowUp') {
      e.preventDefault();
      selectedRowIndex = (selectedRowIndex === null || selectedRowIndex <= 0)
        ? rows.length - 1
        : selectedRowIndex - 1;
      highlightAndSend(rows, selectedRowIndex, table);
    }

    if (e.key === 'Enter' && selectedRowIndex !== null) {
      e.preventDefault();
      highlightAndSend(rows, selectedRowIndex, table);
    }
  });

  // --- Click chuột chọn dòng ---
  $('#tfTable tbody').on('click', 'tr', function () {
    const rows = table.rows({ search: 'applied' }).nodes();
    selectedRowIndex = table.row(this).index();
    highlightAndSend(rows, selectedRowIndex, table);
  });
});

// --- Hàm tô sáng dòng và gửi dữ liệu về form ---
function highlightAndSend(rows, index, table) {
  $(rows).removeClass('selected'); // clear highlight
  const selectedRow = $(rows).eq(index).addClass('selected');

  const rowData = table.row(index).data();
  if (rowData) {
    const message = {
      type: 'fill-form',
      data: {
        tf_id: rowData.tf_id || '',
        tf_topic: rowData.tf_topic || '',
        tf_question: rowData.tf_question || '',
        tf_statement1: rowData.tf_statement1 || '',
        tf_statement2: rowData.tf_statement2 || '',
        tf_statement3: rowData.tf_statement3 || '',
        tf_statement4: rowData.tf_statement4 || '',
        tf_correct_answer1: rowData.tf_correct_answer1 || '',
        tf_correct_answer2: rowData.tf_correct_answer2 || '',
        tf_correct_answer3: rowData.tf_correct_answer3 || '',
        tf_correct_answer4: rowData.tf_correct_answer4 || '',
        tf_image_url: rowData.tf_image_url || ''
      }
    };
    window.parent.postMessage(message, '*');
  }
}
