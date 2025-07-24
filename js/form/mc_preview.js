document.addEventListener('DOMContentLoaded', function () {
  // Xem trước từng phần tử (câu hỏi hoặc đáp án) — hỗ trợ auto-preview khi đang gõ
  document.querySelectorAll('.toggle-preview').forEach(btn => {
    btn.addEventListener('click', function () {
      const targetId = this.dataset.target;
      const input = document.getElementById(targetId);
      const preview = document.getElementById(`preview-${targetId}`);

      if (!input || !preview) return;

      const updatePreview = () => {
        preview.innerHTML = escapeHTML(input.value);
        if (window.MathJax && MathJax.typesetPromise) {
          MathJax.typesetPromise([preview]).catch(err => console.error(err.message));
        }
      };

      if (preview.style.display === 'none' || !preview.style.display) {
        preview.style.display = 'block';
        updatePreview();
        input.addEventListener('input', updatePreview);
        // Gắn hàm để có thể huỷ sau này
        input._mc_preview_listener = updatePreview;
      } else {
        preview.style.display = 'none';
        if (input._mc_preview_listener) {
          input.removeEventListener('input', input._mc_preview_listener);
          delete input._mc_preview_listener;
        }
      }
    });
  });

  // Xem trước toàn bộ nội dung
  const toggleBtn = document.getElementById('mcTogglePreview');
  const previewZone = document.getElementById('mcPreview');
  const previewContent = document.getElementById('mcPreviewContent');

  if (toggleBtn && previewZone && previewContent) {
    toggleBtn.addEventListener('click', function () {
      if (previewZone.style.display === 'none' || !previewZone.style.display) {
        const topic = document.getElementById('mc_topic')?.value || '';
        const question = document.getElementById('mc_question')?.value || '';
        let imageHTML = '';
        const imgEl = document.querySelector('.mc-image-preview img');
        if (imgEl && imgEl.src) {
          imageHTML = `<div class="preview-image"><img src="${imgEl.src}" alt="Hình minh hoạ"></div>`;
        }
        const opts = ['A.', 'B.', 'C.', 'D.'].map(letter => {
        const idx = letterToIndex(letter);
        const value = document.getElementById(`mc_answer${idx}`)?.value || '';
        return `<li>${letter} ${escapeHTML(value)}</li>`; 
        }).join('');


        

        previewContent.innerHTML = `
          <div class="preview-block">
            <h4>Chủ đề: ${escapeHTML(topic)}</h4>
            <p><strong>Câu hỏi:</strong> ${escapeHTML(question)}</p>
            <ul class="preview-options">${opts}</ul>
            ${imageHTML}
          </div>
        `;
        previewZone.style.display = 'block';

        if (window.MathJax && MathJax.typesetPromise) {
          MathJax.typesetPromise([previewContent]).catch(err => console.error(err.message));
        }
      } else {
        previewZone.style.display = 'none';
      }
    });
  }

  // Chuyển từ A–D sang index 1–4
  function letterToIndex(letter) {
    const map = { A: 1, B: 2, C: 3, D: 4 };
    return map[letter] || 1;
  }

  // Escape để tránh lỗi với innerHTML
  function escapeHTML(str) {
    return str
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/\n/g, "<br>");
  }
});
