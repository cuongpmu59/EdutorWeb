// js/tf/tf_table_arrow_key.js

let selectedRowIndex = null;

$(document).ready(function () {
  const table = $('#tfTable').DataTable();

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
  $('#tfTable tbody').on('click', 'tr', function () {
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

    // Gửi dữ liệu sang form cha tf_form.php
    window.parent.postMessage(message, '*');
  }
}
