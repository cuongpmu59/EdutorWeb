$(document).ready(function () {
  const table = $('#mcTable').DataTable();

  // Bắt sự kiện click vào một dòng
  $('#mcTable tbody').on('click', 'tr', function () {
    if (!$(this).hasClass('selected')) {
      table.$('tr.selected').removeClass('selected');
      $(this).addClass('selected');

      const cells = $(this).find('td');
      const data = {
        type: 'mc_select_row',
        mc_id: cells.eq(0).data('raw'),
        mc_topic: cells.eq(1).data('raw'),
        mc_question: cells.eq(2).data('raw'),
        mc_answer1: cells.eq(3).data('raw'),
        mc_answer2: cells.eq(4).data('raw'),
        mc_answer3: cells.eq(5).data('raw'),
        mc_answer4: cells.eq(6).data('raw'),
        mc_correct_answer: cells.eq(7).data('raw'),
        mc_image_url: cells.eq(8).data('raw')
      };

      // Gửi dữ liệu về form cha
      window.parent.postMessage(data, '*');
    }
  });
});
