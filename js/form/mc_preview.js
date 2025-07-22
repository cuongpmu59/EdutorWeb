document.addEventListener('DOMContentLoaded', function () {
  // Xem trước từng phần tử (câu hỏi hoặc đáp án)
  document.querySelectorAll('.toggle-preview').forEach(btn => {
    btn.addEventListener('click', function () {
      const targetId = this.dataset.target;
      const input = document.getElementById(targetId);
      const preview = document.getElementById(`preview-${targetId}`);

      if (!input || !preview) return;

      if (preview.style.display === 'none' || !preview.style.display) {
        preview.innerHTML = input.value.trim();
        preview.style.display = 'block';

        if (window.MathJax) {
          MathJax.typesetPromise([preview]);
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

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      if (previewZone.style.display === 'none' || !previewZone.style.display) {
        // Lấy dữ liệu từ form
        const topic = document.getElementById('mc_topic')?.value || '';
        const question = document.getElementById('mc_question')?.value || '';

        const letters = ['A', 'B', 'C', 'D'];
        const optionsHTML = letters.map(letter => {
          const input = document.getElementById(`mc_opt_${letter}`);
          const text = input?.value || '';
          return `<li><strong>${letter}.</strong> ${text}</li>`;
        }).join('');

        // Lấy ảnh nếu có
        let imageHTML = '';
        const img = document.querySelector('.mc-image-preview img');
        if (img?.src) {
          imageHTML = `<div class="preview-image"><img src="${img.src}" alt="Hình minh hoạ"></div>`;
        }

        // Cập nhật nội dung xem trước
        previewContent.innerHTML = `
          <div class="preview-block">
            <h4>Chủ đề: ${topic}</h4>
            <p><strong>Câu hỏi:</strong> ${question}</p>
            <ul class="preview-options">${optionsHTML}</ul>
            ${imageHTML}
          </div>
        `;

        previewZone.style.display = 'block';
        if (window.MathJax) {
          MathJax.typesetPromise([previewContent]);
        }
      } else {
        previewZone.style.display = 'none';
      }
    });
  }
});
