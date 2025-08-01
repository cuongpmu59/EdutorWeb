// js/mc/mc_table_arrow_key.js

let selectedRowIndex = null;

$(document).ready(function () {
  const table = $('#mcTable').DataTable();

  // Khi nhấn phím lên hoặc xuống
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
      moveAndSendData(rows, selectedRowIndex, table);
    }

    if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (selectedRowIndex === null || selectedRowIndex <= 0) {
        selectedRowIndex = rows.length - 1;
      } else {
        selectedRowIndex--;
      }
      moveAndSendData(rows, selectedRowIndex, table);
    }
  });

  // Bấm chuột chọn dòng cũng đánh dấu selectedRowIndex
  $('#mcTable tbody').on('click', 'tr', function () {
    selectedRowIndex = table.row(this).index();
  });
});

// Hàm xử lý tô sáng và gửi dữ liệu
function moveAndSendData(rows, index, table) {
  $(rows).removeClass('selected');
  const selectedRow = $(rows).eq(index);
  selectedRow.addClass('selected');

  const rowData = table.row(index).data();
  if (rowData) {
    const message = {
      type: 'fill-form',
      data: {
        mc_id: rowData.mc_id,
        mc_topic: rowData.mc_topic,
        mc_question: rowData.mc_question,
        mc_answer1: rowData.mc_answer1,
        mc_answer2: rowData.mc_answer2,
        mc_answer3: rowData.mc_answer3,
        mc_answer4: rowData.mc_answer4,
        mc_correct_answer: rowData.mc_correct_answer,
        mc_image_url: rowData.mc_image_url
      }
    };
    // Gửi sang parent (form cha)
    window.parent.postMessage(message, '*');
  }
}
