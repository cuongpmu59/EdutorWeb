// =======================
// tf_form_preview.js
// =======================

// Danh sách các trường liên quan
const previewFields = [
  { id: 'tf_question', label: 'Câu hỏi' },
  { id: 'tf_statement1', label: '1' },
  { id: 'tf_statement2', label: '2' },
  { id: 'tf_statement3', label: '3' },
  { id: 'tf_statement4', label: '4' }
];

// Cập nhật preview cho từng trường riêng lẻ
function updatePreview(id) {
  const inputEl = document.getElementById(id);
  const previewEl = document.getElementById(`preview-${id}`);
  if (!inputEl || !previewEl) return;

  const value = inputEl.value.trim();
  previewEl.innerHTML = value; // Giữ nguyên cả text và LaTeX

  if (window.MathJax) {
    MathJax.typesetPromise([previewEl]);
  }
}

// Cập nhật toàn bộ preview
function updateFullPreview() {
  const topic = document.getElementById('tf_topic')?.value.trim() || '';

  let html = `<p><strong>Chủ đề:</strong> ${topic}</p>`;

  // Câu hỏi
  const question = document.getElementById('tf_question')?.value.trim() || '';
  html += `<p><strong>Câu hỏi:</strong> ${question}</p>`;

  // Ảnh minh họa (nếu có) -> ngay sau câu hỏi
  const imageUrl = document.getElementById('tf_image_url')?.value;
  if (imageUrl) {
    html += `<div class="preview-image">
      <img src="${imageUrl}" alt="Ảnh minh họa">
    </div>`;
  }

  // Các phát biểu
  html += `<ul class="preview-options">`;
  previewFields
    .filter(f => f.id !== 'tf_question')
    .forEach(({ id, label }, idx) => {
      const value = document.getElementById(id)?.value.trim() || '';
      const correctVal = document.getElementById(`tf_correct_answer${idx + 1}`)?.value;
      const isCorrect = (correctVal === '1'); // 1 = đúng, 0 = sai

      html += `
        <li class="${isCorrect ? 'correct-answer' : ''}">
          <strong>${label}.</strong> ${value}
        </li>`;
    });
  html += `</ul>`;

  const fullPreviewEl = document.getElementById('tfPreviewContent');
  if (fullPreviewEl) {
    fullPreviewEl.innerHTML = html;

    if (window.MathJax) {
      MathJax.typesetPromise([fullPreviewEl]);
    }
  }
}

// Thiết lập realtime preview cho tất cả trường
function setupRealtimePreview() {
  previewFields.forEach(({ id }) => {
    const inputEl = document.getElementById(id);
    if (inputEl) {
      inputEl.addEventListener('input', () => {
        updatePreview(id);
        updateFullPreview();
      });
    }
  });

  // Khi chọn đáp án đúng (nhiều correct_answer{i})
  for (let i = 1; i <= 4; i++) {
    const correctEl = document.getElementById(`tf_correct_answer${i}`);
    if (correctEl) {
      correctEl.addEventListener('change', updateFullPreview);
    }
  }

  // Khi thay đổi chủ đề
  const topicEl = document.getElementById('tf_topic');
  if (topicEl) {
    topicEl.addEventListener('input', updateFullPreview);
  }

  // Khi thay đổi ảnh minh họa
  const imageEl = document.getElementById('tf_image_url');
  if (imageEl) {
    imageEl.addEventListener('input', updateFullPreview);
  }
}

// Thiết lập nút con mắt toggle preview từng trường
function setupPreviewToggle() {
  const toggleButtons = document.querySelectorAll('.toggle-preview');
  toggleButtons.forEach(button => {
    const targetId = button.getAttribute('data-target');
    const previewEl = document.getElementById(`preview-${targetId}`);

    button.addEventListener('click', () => {
      if (!previewEl) return;
      const isVisible = window.getComputedStyle(previewEl).display !== 'none';
      previewEl.style.display = isVisible ? 'none' : 'block';

      if (!isVisible) {
        updatePreview(targetId);
      }
    });
  });
}

// Thiết lập nút toggle xem trước toàn bộ
function setupFullPreviewToggle() {
  const btn = document.getElementById('tfTogglePreview');
  const zone = document.getElementById('tfPreview');

  if (!btn || !zone) return;

  btn.addEventListener('click', () => {
    const isVisible = window.getComputedStyle(zone).display !== 'none';

    if (isVisible) {
      zone.style.display = 'none';
      btn.classList.remove('active');
    } else {
      zone.style.display = 'block'; // hoặc 'flex' tùy CSS
      btn.classList.add('active');
      updateFullPreview();
    }
  });
}

// Khởi tạo
document.addEventListener('DOMContentLoaded', () => {
  setupRealtimePreview();
  setupPreviewToggle();
  setupFullPreviewToggle();
  updateFullPreview(); // load sẵn khi mở form
});
