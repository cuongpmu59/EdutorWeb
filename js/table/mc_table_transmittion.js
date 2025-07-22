function sendRowData(row) {
    const data = row.data();
    if (!data) return;
  
    const $row = row.node();
    const cells = $($row).find('td');
  
    const message = {
      type: "mc_select_row",
      data: {
        mc_id: cells.eq(0).data('raw'),
        mc_topic: cells.eq(1).data('raw'),
        mc_question: cells.eq(2).data('raw'),
        mc_answer1: cells.eq(3).data('raw'),
        mc_answer2: cells.eq(4).data('raw'),
        mc_answer3: cells.eq(5).data('raw'),
        mc_answer4: cells.eq(6).data('raw'),
        mc_correct_answer: cells.eq(7).data('raw'),
        mc_image_url: cells.eq(8).find('img').length > 0
          ? cells.eq(8).find('img').attr('src')
          : ''
      }
    };
  
    // Gửi dữ liệu về form cha
    window.parent.postMessage(message, "*");
  }
  
  $(document).ready(function () {
    // Xử lý click chọn dòng
    $('#mcTable tbody').on('click', 'tr', function () {
      mcTable.$('tr.selected').removeClass('selected');
      $(this).addClass('selected')[0].scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });
  
      sendRowData(mcTable.row(this));
    });
  
    // Tự động chọn dòng đầu tiên khi bảng đã sẵn sàng
    $('#mcTable tbody tr:first').trigger('click');
  });
  