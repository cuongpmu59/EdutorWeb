// File: js/table/mc_table_transmittion.js

$(document).ready(function () {
  const table = $('#mcTable').DataTable();

  $('#mcTable tbody').on('click', 'tr', function () {
    const $tds = $(this).find('td');

    const message = {
      type: 'mc_select_row',
      mc_id: $tds.eq(0).data('raw'),
      mc_topic: $tds.eq(1).data('raw'),
      mc_question: $tds.eq(2).data('raw'),
      mc_answer1: $tds.eq(3).data('raw'),
      mc_answer2: $tds.eq(4).data('raw'),
      mc_answer3: $tds.eq(5).data('raw'),
      mc_answer4: $tds.eq(6).data('raw'),
      mc_correct_answer: $tds.eq(7).data('raw'),
      mc_image_url: $tds.eq(8).find('img').attr('src') || ''
    };

    window.parent.postMessage(message, window.location.origin);
  });
});
