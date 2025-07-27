document.addEventListener("DOMContentLoaded", function () {
  const table = $('#mcTable').DataTable();
  let currentRowIndex = 0;

  const highlightRow = (index) => {
    $('#mcTable tbody tr').removeClass('row-selected');
    const $rows = $('#mcTable tbody tr');
    if (index >= 0 && index < $rows.length) {
      const $row = $rows.eq(index);
      $row.addClass('row-selected');
      sendRowDataToParent($row);
      $row[0].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
  };

  const sendRowDataToParent = ($row) => {
    const $cells = $row.find('td');
    if ($cells.length < 9) return;

    const data = {
      type: 'mc_select_row',  // tên sự kiện
      mc_id: $cells.eq(0).data('raw'),
      mc_topic: $cells.eq(1).data('raw'),
      mc_question: $cells.eq(2).data('raw'),
      mc_answer1: $cells.eq(3).data('raw'),
      mc_answer2: $cells.eq(4).data('raw'),
      mc_answer3: $cells.eq(5).data('raw'),
      mc_answer4: $cells.eq(6).data('raw'),
      mc_correct_answer: $cells.eq(7).data('raw'),
      mc_image_url: $cells.eq(8).data('raw')
    };

    window.parent.postMessage(data, '*'); // gửi dữ liệu cho form cha
  };

  $(document).on('keydown', function (e) {
    const rowCount = $('#mcTable tbody tr').length;
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (currentRowIndex < rowCount - 1) {
        currentRowIndex++;
        highlightRow(currentRowIndex);
      }
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (currentRowIndex > 0) {
        currentRowIndex--;
        highlightRow(currentRowIndex);
      }
    }
  });

  // Tự động chọn dòng đầu tiên khi load
  highlightRow(currentRowIndex);
});
