window.addEventListener('message', function (event) {
    const data = event.data;
    if (data.type === 'mc_select_row') {
      $('#mc_topic').val(data.mc_topic);
      $('#mc_question').val(data.mc_question);
      $('#mc_answer1').val(data.mc_answer1);
      $('#mc_answer2').val(data.mc_answer2);
      $('#mc_answer3').val(data.mc_answer3);
      $('#mc_answer4').val(data.mc_answer4);
      $('#mc_correct_answer').val(data.mc_correct_answer);
  
      if (data.mc_image_url) {
        $('#mc_preview_image').attr('src', data.mc_image_url).show();
      } else {
        $('#mc_preview_image').attr('src', '').hide();
      }
    }
  });
  