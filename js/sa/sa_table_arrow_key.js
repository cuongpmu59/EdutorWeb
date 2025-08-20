// js/sa/sa_table_arrow_key.js

let selectedRowIndex = null;

$(document).ready(function () {
  const table = $('#saTable').DataTable();

  // --- Xử lý phím lên/xuống ---
  $(document).on('keydown', function (e) {
    const rows = table.rows({ search: 'applied' }).nodes();
    if (!rows.length) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (selectedRowIndex === null || selectedRowIndex >= rows.length - 1) {
        selectedRowIndex = 0;
      } else {
        selectedRowIndex++;
      }
      highlightAndSend(rows, selectedRowIndex, table);
    }

    if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (selectedRowIndex === null || selectedRowIndex <= 0) {
        selectedRowIndex = rows.length - 1;
      } else {
        selectedRowIndex--;
      }
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
  // Xóa highlight ở tất cả các dòng
  $(rows).removeClass('selected');

  // Tô sáng dòng đang chọn
  const selectedRow = $(rows).eq(index);
  selectedRow.addClass('selected');

  // Lấy dữ liệu dòng
  const rowData = table.row(index).data();
  if (rowData) {
    const message = {
      type: 'fill-form',
      data: {
        sa_id: rowData.sa_id || '',
        sa_topic: rowData.sa_topic || '',
        sa_question: rowData.sa_question || '',
        sa_correct_answer: rowData.sa_correct_answer || '',
        sa_image_url: rowData.sa_image_url || ''
      }
    };

    // Gửi dữ liệu sang form cha sa_form.php
    window.parent.postMessage(message, '*');
  }
}
