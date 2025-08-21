// js/sa/sa_table_arrow_key.js
let selectedRowIndex = null;

$(document).ready(function () {
  const table = $('#saTable').DataTable();

  // Reset highlight khi redraw
  table.on('draw', function () {
    selectedRowIndex = null;
    $('#saTable tbody tr').removeClass('selected');
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
  $('#saTable tbody').on('click', 'tr', function () {
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
        sa_id: rowData.sa_id || '',
        sa_topic: rowData.sa_topic || '',
        sa_question: rowData.sa_question || '',
        sa_answer: rowData.sa_answer || '',
        sa_image_url: rowData.sa_image_url || ''
      }
    };
    window.parent.postMessage(message, '*');
  }
}
