window.addEventListener('message', function (event) {
  const allowedOrigin = window.location.origin;
  if (event.origin !== allowedOrigin) return; // Bảo mật: chỉ nhận message từ chính site

  const data = event.data;

  if (data.type === 'mc_select_row') {
    // Gán nội dung vào các trường
    $('#mc_topic').val(data.mc_topic);
    $('#mc_question').val(data.mc_question);
    $('#mc_answer1').val(data.mc_answer1);
    $('#mc_answer2').val(data.mc_answer2);
    $('#mc_answer3').val(data.mc_answer3);
    $('#mc_answer4').val(data.mc_answer4);
    $('#mc_correct_answer').val(data.mc_correct_answer);

    // Cập nhật ảnh minh hoạ
    if (data.mc_image_url) {
      $('.mc-image-preview').html(`<img id="mc_preview_image" src="${data.mc_image_url}" alt="Hình minh hoạ">`);
    } else {
      $('.mc-image-preview').empty();
    }

    // Cập nhật hoặc thêm trường mc_id ẩn
    const $mcId = $('#mc_id');
    if ($mcId.length) {
      $mcId.val(data.mc_id);
    } else {
      $('#mcForm').append(`<input type="hidden" id="mc_id" name="mc_id" value="${data.mc_id}">`);
    }

    // Cuộn lên đầu form
    document.getElementById('mcForm').scrollIntoView({ behavior: 'smooth' });
  }
});
