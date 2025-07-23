// Xem trước từng trường (câu hỏi, đáp án)
document.querySelectorAll('.toggle-preview').forEach(button => {
  button.addEventListener('click', () => {
    const targetId = button.getAttribute('data-target');
    const input = document.getElementById(targetId);
    const previewBox = document.getElementById(`preview-${targetId}`);

    if (previewBox.style.display === 'none') {
      previewBox.innerHTML = input.value;
      previewBox.style.display = 'block';
      if (window.MathJax && MathJax.typeset) MathJax.typeset([previewBox]);
    } else {
      previewBox.style.display = 'none';
    }
  });
});

// Xem trước toàn bộ nội dung
document.getElementById('mcTogglePreview').addEventListener('click', () => {
  const previewZone = document.getElementById('mcPreview');
  const previewContent = document.getElementById('mcPreviewContent');

  if (previewZone.style.display === 'none' || previewZone.style.display === '') {
    const topic = document.getElementById('mc_topic').value;
    const question = document.getElementById('mc_question').value;
    const answers = [];
    for (let i = 1; i <= 4; i++) {
      answers.push(document.getElementById(`mc_answer${i}`).value);
    }
    const correct = document.getElementById('mc_correct_answer').value;

    let html = `<p><strong>Chủ đề:</strong> ${topic}</p>`;
    html += `<p><strong>Câu hỏi:</strong><br>${question}</p>`;
    answers.forEach((ans, index) => {
      const label = String.fromCharCode(65 + index);
      const mark = (label === correct) ? '✅' : '';
      html += `<p><strong>${label}.</strong> ${ans} ${mark}</p>`;
    });

    const img = document.querySelector('.mc-image-preview img');
    if (img) {
      html += `<p><img src="${img.src}" alt="Hình minh hoạ" style="max-width:300px;"></p>`;
    }

    previewContent.innerHTML = html;
    previewZone.style.display = 'block';

    if (window.MathJax && MathJax.typeset) MathJax.typeset([previewContent]);
  } else {
    previewZone.style.display = 'none';
  }
});
