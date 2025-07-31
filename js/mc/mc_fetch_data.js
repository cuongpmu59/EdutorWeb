// Lắng nghe thông điệp từ iframe (mc_table.php)
window.addEventListener('message', function (event) {
  const message = event.data;

  // Kiểm tra kiểu thông điệp
  if (message.type === 'fill-form' && message.data) {
    const data = message.data;

    // Gán dữ liệu vào các input/textarea
    document.querySelector('#mc_id').value = data.mc_id || '';
    document.querySelector('#mc_topic').value = data.mc_topic || '';
    document.querySelector('#mc_question').value = data.mc_question || '';
    document.querySelector('#mc_answer1').value = data.mc_answer1 || '';
    document.querySelector('#mc_answer2').value = data.mc_answer2 || '';
    document.querySelector('#mc_answer3').value = data.mc_answer3 || '';
    document.querySelector('#mc_answer4').value = data.mc_answer4 || '';
    document.querySelector('#mc_correct_answer').value = data.mc_correct_answer || 'A';

    // Hiển thị ảnh nếu có URL
    const img = document.querySelector('#mc_preview_image');
    if (data.mc_image_url) {
      img.src = data.mc_image_url;
      img.style.display = 'block';
    } else {
      img.src = '';
      img.style.display = 'none';
    }

    // Cuộn form lên trên cùng (tùy chọn)
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});
