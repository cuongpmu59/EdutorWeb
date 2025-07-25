// Danh sách các trường liên quan
const previewFields = [
  { id: 'mc_question', label: 'Câu hỏi' },
  { id: 'mc_answer1', label: 'A' },
  { id: 'mc_answer2', label: 'B' },
  { id: 'mc_answer3', label: 'C' },
  { id: 'mc_answer4', label: 'D' }
];

// Cập nhật preview cho từng trường riêng lẻ
function updatePreview(id) {
  const inputEl = document.getElementById(id);
  const previewEl = document.getElementById(`preview-${id}`);
  if (!inputEl || !previewEl) return;

  const value = inputEl.value.trim();
  previewEl.innerHTML = value ? `\\(${value}\\)` : '';

  if (window.MathJax) {
    MathJax.typesetPromise([previewEl]);
  }
}

// Cập nhật toàn bộ nội dung vào mcPreviewContent
function updateFullPreview() {
  const topic = document.getElementById('mc_topic')?.value.trim() || '';
  const answerSelect = document.getElementById('mc_correct_answer');
  const correct = answerSelect ? answerSelect.value : '';

  let html = `<p><strong>Chủ đề:</strong> ${topic}</p>`;

  previewFields.forEach(({ id, label }) => {
    const value = document.getElementById(id)?.value.trim() || '';
    html += `<p><strong>${label}:</strong> \\(${value}\\)</p>`;
  });

  html += `<p><strong>Đáp án đúng:</strong> ${correct}</p>`;

  const fullPreviewEl = document.getElementById('mcPreviewContent');
  if (fullPreviewEl) {
    fullPreviewEl.innerHTML = html;
    if (window.MathJax) {
      MathJax.typesetPromise([fullPreviewEl]);
    }
  }
}

// Thiết lập sự kiện input realtime cho tất cả trường
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

  // Cập nhật thêm khi chọn đáp án đúng
  const correctAnswerEl = document.getElementById('mc_correct_answer');
  if (correctAnswerEl) {
    correctAnswerEl.addEventListener('change', updateFullPreview);
  }

  // Chủ đề cũng cập nhật realtime
  const topicEl = document.getElementById('mc_topic');
  if (topicEl) {
    topicEl.addEventListener('input', updateFullPreview);
  }
}

// Thiết lập nút con mắt ẩn/hiện preview từng trường
function setupPreviewToggle() {
  const toggleButtons = document.querySelectorAll('.toggle-preview');
  toggleButtons.forEach(button => {
    const targetId = button.getAttribute('data-target');
    const previewEl = document.getElementById(`preview-${targetId}`);

    button.addEventListener('click', () => {
      if (!previewEl) return;
      const isVisible = previewEl.style.display === 'block';
      previewEl.style.display = isVisible ? 'none' : 'block';

      if (!isVisible) {
        updatePreview(targetId);
      }
    });
  });
}

// Thiết lập toggle xem trước toàn bộ
function setupFullPreviewToggle() {
  const btn = document.getElementById('mcTogglePreview');
  const zone = document.getElementById('mcPreview');

  if (btn && zone) {
    btn.addEventListener('click', () => {
      const isVisible = zone.style.display === 'block';
      zone.style.display = isVisible ? 'none' : 'block';
      if (!isVisible) updateFullPreview();
    });
  }
}

// Khởi tạo tất cả khi DOM sẵn sàng
document.addEventListener('DOMContentLoaded', () => {
  setupRealtimePreview();
  setupPreviewToggle();
  setupFullPreviewToggle();
});
