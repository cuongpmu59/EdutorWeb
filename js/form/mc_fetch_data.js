// Lắng nghe dữ liệu gửi từ iframe bảng câu hỏi
window.addEventListener('message', function (event) {
  const message = event.data;

  // Kiểm tra đúng loại message từ iframe
  if (message.type === 'mc_select_row' && message.payload) {
    console.log('[mc_fetch_data] Nhận dữ liệu:', message.payload); // ✅ kiểm tra
    fillForm(message.payload);
  }
});

// Hàm điền dữ liệu vào form
function fillForm(data) {
  document.getElementById('mc_id').value = data.mc_id || '';
  document.getElementById('mc_topic').value = data.mc_topic || '';
  document.getElementById('mc_question').value = data.mc_question || '';
  document.getElementById('mc_answer1').value = data.mc_answer1 || '';
  document.getElementById('mc_answer2').value = data.mc_answer2 || '';
  document.getElementById('mc_answer3').value = data.mc_answer3 || '';
  document.getElementById('mc_answer4').value = data.mc_answer4 || '';
  document.getElementById('mc_correct_answer').value = data.mc_correct_answer || '';

  // Xử lý ảnh minh hoạ
  const img = document.getElementById('mc_image_preview');
  const hiddenInput = document.getElementById('existing_image');
  if (data.mc_image_url) {
    img.src = data.mc_image_url;
    img.style.display = 'block';
    hiddenInput.value = data.mc_image_url;
  } else {
    img.src = '';
    img.style.display = 'none';
    hiddenInput.value = '';
  }

  // Gọi lại MathJax nếu có
  if (window.MathJax && typeof MathJax.typeset === 'function') {
    MathJax.typeset();
  }
}


