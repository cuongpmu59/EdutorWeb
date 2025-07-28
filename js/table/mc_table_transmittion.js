$(document).ready(function () {
  const table = $('#mc_table').DataTable();

  $('#mc_table tbody').on('click', 'tr', function () {
    const row = table.row(this).node();
    const data = table.row(this).data();
    if (!data) return;

    // Lấy URL từ ảnh trong cột 8 (dùng jQuery tìm thẻ <img> trong cột 8 của dòng đó)
    const imgSrc = $('td:eq(8) img', row).attr('src') || '';

    const message = {
      type: 'mc_select_row',
      mc_id: data[0],
      mc_topic: data[1],
      mc_question: data[2],
      mc_answer1: data[3],
      mc_answer2: data[4],
      mc_answer3: data[5],
      mc_answer4: data[6],
      mc_correct_answer: data[7],
      mc_image_url: imgSrc
    };

    window.parent.postMessage(message, window.location.origin);
  });
});
