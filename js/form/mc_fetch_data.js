function fetchQuestionById(mc_id) {
  if (!mc_id) return;

  fetch(`../../includes/mc_get_data.php?mc_id=${mc_id}`)
    .then(res => res.json())
    .then(res => {
      if (!res.success || !res.data) {
        alert('Không tìm thấy dữ liệu câu hỏi.');
        return;
      }

      const mc = res.data;

      // Gán dữ liệu vào các trường
      document.getElementById('mc_id').value = mc.mc_id || '';
      document.getElementById('mc_topic').value = mc.mc_topic || '';
      document.getElementById('mc_question').value = mc.mc_question || '';

      for (let i = 1; i <= 4; i++) {
        const input = document.getElementById(`mc_answer${i}`);
        if (input) input.value = mc[`mc_answer${i}`] || '';
      }

      const select = document.getElementById('mc_correct_answer');
      if (select) select.value = mc.mc_correct_answer || '';

      // Hiển thị ảnh minh hoạ
      const preview = document.querySelector('.mc-image-preview');
      preview.innerHTML = '';
      if (mc.mc_image_url) {
        const img = document.createElement('img');
        img.src = mc.mc_image_url;
        img.alt = 'Hình minh hoạ';
        preview.appendChild(img);
      }

      // Gán lại giá trị ảnh đang tồn tại (để không ghi đè nếu không thay đổi)
      let existing = document.querySelector('input[name="existing_image"]');
      if (!existing) {
        existing = document.createElement('input');
        existing.type = 'hidden';
        existing.name = 'existing_image';
        document.getElementById('mcForm').appendChild(existing);
      }
      existing.value = mc.mc_image_url || '';

      // Cập nhật MathJax (nếu có công thức)
      if (window.MathJax?.typeset) {
        MathJax.typeset();
      }
    })
    .catch(err => {
      console.error('Lỗi khi lấy dữ liệu câu hỏi:', err);
    });
}

// Lắng nghe sự kiện từ iframe
window.addEventListener('message', event => {
  const message = event.data;
  if (message?.type === 'mc_select_row' && message.payload?.mc_id) {
    fetchQuestionById(message.payload.mc_id);
  }
});
