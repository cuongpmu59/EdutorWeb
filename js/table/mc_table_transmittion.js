$(document).ready(function () {
  $('#mcTable tbody').on('click', 'tr', function () {
    const $cells = $(this).children('td');
    const data = {
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

    // Gửi sang parent (form) qua postMessage
    window.parent.postMessage({ type: 'mc_select_row', data }, '*');
  });
});
