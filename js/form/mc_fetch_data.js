// Lắng nghe dữ liệu gửi từ iframe mc_table
window.addEventListener('message', function (event) {
  if (event.data && event.data.type === 'mc_select_row') {
    const mc_id = event.data.data.mc_id;

    // Gửi request để lấy dữ liệu câu hỏi từ server
    $.getJSON('../../includes/mc_get_data.php', { mc_id }, function (res) {
      if (res.error) {
        alert(res.error);
        return;
      }

      const data = res.data || res;

      // Cập nhật hidden mc_id
      $('#mc_id').remove(); // Xoá nếu đã tồn tại
      $('<input>', {
        type: 'hidden',
        id: 'mc_id',
        name: 'mc_id',
        value: data.mc_id
      }).appendTo('#mcForm');

      // Điền dữ liệu vào các trường input
      $('#mc_topic').val(data.mc_topic);
      $('#mc_question').val(data.mc_question);
      $('#mc_answer1').val(data.mc_answer1);
      $('#mc_answer2').val(data.mc_answer2);
      $('#mc_answer3').val(data.mc_answer3);
      $('#mc_answer4').val(data.mc_answer4);
      $('#mc_correct_answer').val(data.mc_correct_answer);

      // Xử lý ảnh minh hoạ nếu có
      if (data.mc_image_url) {
        $('.mc-image-preview').html(`<img src="${data.mc_image_url}" alt="Hình minh hoạ">`);

        // Gán hidden để lưu đường dẫn ảnh cũ
        $('input[name=existing_image]').remove();
        $('<input>', {
          type: 'hidden',
          name: 'existing_image',
          value: data.mc_image_url
        }).appendTo('#mcForm');
      } else {
        // Xoá ảnh nếu không có
        $('.mc-image-preview').empty();
        $('input[name=existing_image]').remove();
      }

      // Cuộn về đầu form
      $('html, body').animate({ scrollTop: 0 }, 300);
    });
  }
});
