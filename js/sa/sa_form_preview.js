// =======================
// sa_form_preview.js
// =======================

// Danh sách các trường liên quan
const previewFields = [
  { id: 'sa_question', label: 'Câu hỏi' },
  { id: 'sa_correct_answer', label: 'Đáp án' }
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
  const topic = document.getElementById('sa_topic')?.value.trim() || '';

  let html = `<p><strong>Chủ đề:</strong> ${topic}</p>`;

  // Câu hỏi
  const question = document.getElementById('sa_question')?.value.trim() || '';
  html += `<p><strong>Câu hỏi:</strong> ${question}</p>`;

  // Ảnh minh họa (nếu có) -> ngay sau câu hỏi
  const imageUrl = document.getElementById('sa_image_url')?.value;
  if (imageUrl) {
    html += `<div class="preview-image">
      <img src="${imageUrl}" alt="Ảnh minh họa">
    </div>`;
  }

  // Đáp án đúng
  const correct = document.getElementById('sa_correct_answer')?.value.trim() || '';
  html += `<p><strong>Đáp án đúng:</strong> ${correct}</p>`;

  const fullPreviewEl = document.getElementById('saPreviewContent');
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

  // Khi thay đổi chủ đề
  const topicEl = document.getElementById('sa_topic');
  if (topicEl) {
    topicEl.addEventListener('input', updateFullPreview);
  }

  // Khi thay đổi ảnh minh họa
  const imageEl = document.getElementById('sa_image_url');
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
  const btn = document.getElementById('saTogglePreview');
  const zone = document.getElementById('saPreview');

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
