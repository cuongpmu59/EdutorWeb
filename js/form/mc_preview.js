document.addEventListener('DOMContentLoaded', function () {
  // Xem trước từng phần tử (câu hỏi hoặc đáp án)
  document.querySelectorAll('.toggle-preview').forEach(btn => {
    btn.addEventListener('click', function () {
      const targetId = this.dataset.target;
      const input = document.getElementById(targetId);
      const preview = document.getElementById(`preview-${targetId}`);

      if (preview.style.display === 'none' || !preview.style.display) {
        preview.innerHTML = input.value;
        preview.style.display = 'block';
        if (window.MathJax) MathJax.typesetPromise([preview]);
      } else {
        preview.style.display = 'none';
      }
    });
  });

  // Xem trước toàn bộ nội dung
  const toggleBtn = document.getElementById('mcTogglePreview');
  const previewZone = document.getElementById('mcPreview');
  const previewContent = document.getElementById('mcPreviewContent');

  toggleBtn.addEventListener('click', function () {
    if (previewZone.style.display === 'none' || !previewZone.style.display) {
      // Hiển thị toàn bộ nội dung xem trước
      const topic = document.getElementById('mc_topic').value;
      const question = document.getElementById('mc_question').value;
      const opts = ['A', 'B', 'C', 'D'].map(letter => {
        const value = document.getElementById(`mc_opt_${letter}`).value;
        return `<li><strong>${letter}.</strong> ${value}</li>`;
      }).join('');

      let imageHTML = '';
      const imgEl = document.querySelector('.mc-image-preview img');
      if (imgEl) {
        imageHTML = `<div class="preview-image"><img src="${imgEl.src}" alt="Hình minh hoạ"></div>`;
      }

      previewContent.innerHTML = `
        <div class="preview-block">
          <h4>Chủ đề: ${topic}</h4>
          <p><strong>Câu hỏi:</strong> ${question}</p>
          <ul class="preview-options">${opts}</ul>
          ${imageHTML}
        </div>
      `;
      previewZone.style.display = 'block';
      if (window.MathJax) MathJax.typesetPromise([previewContent]);
    } else {
      previewZone.style.display = 'none';
    }
  });
});
