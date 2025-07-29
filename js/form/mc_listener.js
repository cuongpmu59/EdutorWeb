window.addEventListener('message', function (e) {
  if (e.data?.type === 'fill-form') {
    const q = e.data.data;

    $('#mc_id').val(q.mc_id);
    $('#topic').val(q.mc_topic);
    $('#question').val(q.mc_question);
    $('#answer1').val(q.mc_answer1);
    $('#answer2').val(q.mc_answer2);
    $('#answer3').val(q.mc_answer3);
    $('#answer4').val(q.mc_answer4);
    $('#answer').val(q.mc_correct_answer);

    if (q.mc_image_url) {
      $('#existing-image').attr('src', q.mc_image_url).show();
      $('#existing_image').val(q.mc_image_url);
    } else {
      $('#existing-image').hide();
      $('#existing_image').val('');
    }
  }
});
