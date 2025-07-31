// Lắng nghe thông điệp từ iframe (mc_table.php)
window.addEventListener('message', function (event) {
  const message = event.data;

  // Đảm bảo đúng định dạng message
  if (message?.type === 'fill-form' && message.data) {
    const data = message.data;

    // Gán dữ liệu vào form
    document.querySelector('#mc_id').value = data.mc_id || '';
    document.querySelector('#mc_topic').value = data.mc_topic || '';
    document.querySelector('#mc_question').value = data.mc_question || '';
    document.querySelector('#mc_answer1').value = data.mc_answer1 || '';
    document.querySelector('#mc_answer2').value = data.mc_answer2 || '';
    document.querySelector('#mc_answer3').value = data.mc_answer3 || '';
    document.querySelector('#mc_answer4').value = data.mc_answer4 || '';
    document.querySelector('#mc_correct_answer').value = data.mc_correct_answer || 'A';

    // Hiển thị ảnh nếu có
    const preview = document.querySelector('#mc_preview_image');
    if (data.mc_image_url) {
      preview.innerHTML = ''; // Xoá ảnh cũ nếu có
      const img = document.createElement('img');
      img.src = data.mc_image_url;
      img.alt = 'Ảnh câu hỏi';
      img.style.maxWidth = '120px';
      img.style.marginTop = '8px';
      preview.appendChild(img);
    } else {
      preview.innerHTML = '';
    }

    // Cuộn lên đầu form nếu cần
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});
