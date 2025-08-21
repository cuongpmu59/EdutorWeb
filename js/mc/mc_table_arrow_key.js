// js/mc/mc_table_arrow_key.js
let selectedRowIndex = null;

$(document).ready(function () {
  const table = $('#mcTable').DataTable();

  // Reset highlight khi redraw
  table.on('draw', function () {
    selectedRowIndex = null;
    $('#mcTable tbody tr').removeClass('selected');
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
  $('#mcTable tbody').on('click', 'tr', function () {
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
        mc_id: rowData.mc_id || '',
        mc_topic: rowData.mc_topic || '',
        mc_question: rowData.mc_question || '',
        mc_answer1: rowData.mc_answer1 || '',
        mc_answer2: rowData.mc_answer2 || '',
        mc_answer3: rowData.mc_answer3 || '',
        mc_answer4: rowData.mc_answer4 || '',
        mc_correct_answer: rowData.mc_correct_answer || '',
        mc_image_url: rowData.mc_image_url || ''
      }
    };
    window.parent.postMessage(message, '*');
  }
}
