document.addEventListener('DOMContentLoaded', function () {
  // Xem trước từng phần tử (câu hỏi hoặc đáp án)
  document.querySelectorAll('.toggle-preview').forEach(btn => {
    btn.addEventListener('click', function () {
      const targetId = this.dataset.target;
      const input = document.getElementById(targetId);
      const preview = document.getElementById(`preview-${targetId}`);

      if (!input || !preview) return;

      const isHidden = preview.style.display === 'none' || !preview.style.display;
      if (isHidden) {
        preview.innerHTML = escapeHtml(input.value);
        preview.style.display = 'block';
        if (window.MathJax?.typesetPromise) {
          MathJax.typesetPromise([preview]).catch(err => console.warn("MathJax error:", err));
        }
      } else {
        preview.style.display = 'none';
      }
    });
  });

  // Xem trước toàn bộ nội dung
  const toggleBtn = document.getElementById('mcTogglePreview');
  const previewZone = document.getElementById('mcPreview');
  const previewContent = document.getElementById('mcPreviewContent');

  if (toggleBtn && previewZone && previewContent) {
    toggleBtn.addEventListener('click', function () {
      const isHidden = previewZone.style.display === 'none' || !previewZone.style.display;

      if (isHidden) {
        const topic = document.getElementById('mc_topic')?.value || '';
        const question = document.getElementById('mc_question')?.value || '';

        const opts = ['A', 'B', 'C', 'D'].map(letter => {
          const val = document.getElementById(`mc_answer${letterToIndex(letter)}`)?.value || '';
          return `<li><strong>${letter}.</strong> ${escapeHtml(val)}</li>`;
        }).join('');

        const imgEl = document.querySelector('.mc-image-preview img');
        const imageHTML = (imgEl && imgEl.src)
          ? `<div class="preview-image"><img src="${imgEl.src}" alt="Hình minh hoạ"></div>` : '';

        previewContent.innerHTML = `
          <div class="preview-block">
            <h4>Chủ đề: ${escapeHtml(topic)}</h4>
            <p><strong>Câu hỏi:</strong> ${escapeHtml(question)}</p>
            <ul class="preview-options">${opts}</ul>
            ${imageHTML}
          </div>
        `;

        previewZone.style.display = 'block';

        if (window.MathJax?.typesetPromise) {
          MathJax.typesetPromise([previewContent]).catch(err => console.warn("MathJax error:", err));
        }
      } else {
        previewZone.style.display = 'none';
      }
    });
  }

  // Chuyển từ A–D sang index 1–4
  function letterToIndex(letter) {
    return { A: 1, B: 2, C: 3, D: 4 }[letter] || 1;
  }

  // Escape nội dung HTML, trừ công thức toán
  function escapeHtml(text) {
    return text
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')  // giữ nguyên $...$ để MathJax xử lý
      .replace(/>/g, '&gt;');
  }
});
