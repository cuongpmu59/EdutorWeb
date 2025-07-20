document.addEventListener('DOMContentLoaded', () => {
    const viewListBtn = document.getElementById('mc_view_list');
    if (viewListBtn) {
      viewListBtn.addEventListener('click', () => {
        window.location.href = 'mc_list.php';
      });
    }
  
    const mcIdField = document.getElementById('mc_id');
    if (mcIdField) {
      const id = mcIdField.value;
  
      fetch(`api/get_mc_question.php?mc_id=${id}`)
        .then(res => res.json())
        .then(data => {
          if (!data || !data.mc_id) {
            console.error("Không tìm thấy dữ liệu câu hỏi");
            return;
          }
  
          document.getElementById('mc_topic').value = data.mc_topic || '';
          document.getElementById('mc_question').value = data.mc_question || '';
  
          ['A', 'B', 'C', 'D'].forEach(opt => {
            const el = document.getElementById(`mc_opt_${opt}`);
            if (el) el.value = data[`mc_opt_${opt}`] || '';
          });
  
          const answerEl = document.getElementById('mc_answer');
          if (answerEl) answerEl.value = data.mc_answer || '';
  
          const imagePreview = document.querySelector('.mc-image-preview');
          if (imagePreview && data.mc_image_url) {
            imagePreview.innerHTML = `<img src="${data.mc_image_url}" alt="Ảnh minh hoạ">`;
          }
        })
        .catch(err => {
          console.error("Lỗi khi load câu hỏi:", err);
        });
    }
  });
  