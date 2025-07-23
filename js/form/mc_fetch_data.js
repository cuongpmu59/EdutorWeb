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
  
        document.getElementById('mc_id').value = mc.mc_id || '';
        document.getElementById('mc_topic').value = mc.mc_topic || '';
        document.getElementById('mc_question').value = mc.mc_question || '';
  
        for (let i = 1; i <= 4; i++) {
          const input = document.getElementById(`mc_answer${i}`);
          if (input) input.value = mc[`mc_answer${i}`] || '';
        }
  
        const select = document.getElementById('mc_correct_answer');
        if (select) select.value = mc.mc_correct_answer || '';
  
        const preview = document.querySelector('.mc-image-preview');
        preview.innerHTML = '';
        if (mc.mc_image_url) {
          const img = document.createElement('img');
          img.src = mc.mc_image_url;
          img.alt = 'Hình minh hoạ';
          preview.appendChild(img);
        }
  
        let existing = document.querySelector('input[name="existing_image"]');
        if (!existing) {
          existing = document.createElement('input');
          existing.type = 'hidden';
          existing.name = 'existing_image';
          document.getElementById('mcForm').appendChild(existing);
        }
        existing.value = mc.mc_image_url || '';
  
        if (window.MathJax?.typeset) {
          MathJax.typeset();
        }
      })
      .catch(err => {
        console.error('Lỗi khi lấy dữ liệu câu hỏi:', err);
      });
  }
  
  // Lắng nghe từ iframe
  window.addEventListener('message', event => {
    const message = event.data;
    if (message?.type === 'mc_select_row') {
      fetchQuestionById(message.data.mc_id);
    }
  });
  