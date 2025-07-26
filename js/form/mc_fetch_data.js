window.addEventListener('message', function (event) {
  if (event.data?.type === 'mc_select_row') {
    const mc_id = event.data.data.mc_id;

    // Gọi AJAX để lấy dữ liệu chi tiết
    $.ajax({
      url: '../../includes/mc_get_data.php',
      method: 'GET',
      data: { mc_id },
      dataType: 'json',
      success: function (data) {
        if (data && data.mc_id) {
          // Đổ dữ liệu vào form
          $('#mc_id').val(data.mc_id);
          $('#mc_topic').val(data.mc_topic);
          $('#mc_question').val(data.mc_question);
          $('#mc_answer1').val(data.mc_answer1);
          $('#mc_answer2').val(data.mc_answer2);
          $('#mc_answer3').val(data.mc_answer3);
          $('#mc_answer4').val(data.mc_answer4);
          $('#mc_correct_answer').val(data.mc_correct_answer);

          if (data.mc_image_url) {
            $('.mc-image-preview').html(`<img src="${data.mc_image_url}" alt="Hình minh hoạ">`);
            $('input[name=existing_image]').remove();
            $('<input>', {
              type: 'hidden',
              name: 'existing_image',
              value: data.mc_image_url
            }).appendTo('#mcForm');
          } else {
            $('.mc-image-preview').empty();
            $('input[name=existing_image]').remove();
          }

          $('html, body').animate({ scrollTop: 0 }, 300);
        }
      }
    });
  }
});
