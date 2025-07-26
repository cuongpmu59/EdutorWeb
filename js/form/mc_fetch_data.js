window.addEventListener('message', function (event) {
  if (event.data && event.data.type === 'mc_select_row') {
    const data = event.data.data;

    // Đổ dữ liệu vào form (hoặc gọi AJAX nếu muốn)
    $('#mc_id').val(data.mc_id);
    $('#mc_topic').val(data.mc_topic);
    $('#mc_question').val(data.mc_question);
    $('#mc_answer1').val(data.mc_answer1);
    $('#mc_answer2').val(data.mc_answer2);
    $('#mc_answer3').val(data.mc_answer3);
    $('#mc_answer4').val(data.mc_answer4);
    $('#mc_correct_answer').val(data.mc_correct_answer);

    // Hiển thị ảnh nếu có
    if (data.mc_image_url) {
      $('.mc-image-preview').html(`<img src="${data.mc_image_url}" alt="Hình minh hoạ">`);
      $('input[name=existing_image]').remove();
      $('<input>').attr({
        type: 'hidden',
        name: 'existing_image',
        value: data.mc_image_url
      }).appendTo('#mcForm');
    } else {
      $('.mc-image-preview').empty();
      $('input[name=existing_image]').remove();
    }

    // Cuộn lên đầu form để dễ nhìn
    $('html, body').animate({ scrollTop: 0 }, 300);
  }
});
