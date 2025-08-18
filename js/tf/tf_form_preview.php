// tf_form_preview.js

// Danh sách các trường liên quan
const previewFields = [
  { id: 'tf_question', label: 'Câu hỏi' },
  { id: 'tf_statement1', label: 'Mệnh đề 1' },
  { id: 'tf_statement2', label: 'Mệnh đề 2' },
  { id: 'tf_statement3', label: 'Mệnh đề 3' },
  { id: 'tf_statement4', label: 'Mệnh đề 4' }
];

// Cập nhật preview cho từng trường riêng lẻ
function updatePreview(id) {
  const inputEl = document.getElementById(id);
  const previewEl = document.getElementById(`preview-${id}`);
  if (!inputEl || !previewEl) return;

  const value = inputEl.value.trim();
  previewEl.innerHTML = value;

  if (window.MathJax) {
    MathJax.typesetPromise([previewEl]);
  }
}

// Cập nhật toàn bộ nội dung vào tfPreviewContent
function updateFullPreview() {
  const topic = document.getElementById('tf_topic')?.value.trim() || '';

  let html = `<p><strong>Chủ đề:</strong> ${topic}</p>`;

  // Câu hỏi chính
  const questionVal = document.getElementById('tf_question')?.value.trim() || '';
  html += `<p><strong>Câu hỏi:</strong> ${questionVal}</p>`;

  // Các mệnh đề và đáp án đúng/sai
  previewFields
    .filter(f => f.id.startsWith('tf_statement'))
    .forEach(({ id, label }, idx) => {
      const value = document.getElementById(id)?.value.trim() || '';
      const correctEl = document.getElementById(`tf_correct_answer${idx + 1}`);
      const correct = correctEl ? correctEl.value : '';
      if (value) {
        html += `<p><strong>${label}:</strong> ${value} 
                 <em>(Đáp án: ${correct === "1" ? "Đúng" : "Sai"})</em></p>`;
      }
    });

  const fullPreviewEl = document.getElementById('tfPreviewContent');
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

  // Cập nhật khi chọn đúng/sai của từng mệnh đề
  for (let i = 1; i <= 4; i++) {
    const ansEl = document.getElementById(`tf_correct_answer${i}`);
    if (ansEl) {
      ansEl.addEventListener('change', updateFullPreview);
    }
  }

  // Cập nhật khi thay đổi chủ đề
  const topicEl = document.getElementById('tf_topic');
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
      const isVisible = window.getComputedStyle(previewEl).display !== 'none';
      previewEl.style.display = isVisible ? 'none' : 'block';

      if (!isVisible) {
        updatePreview(targetId);
      }
    });
  });
}

// Thiết lập toggle xem trước toàn bộ
function setupFullPreviewToggle() {
  const btn = document.getElementById('tfTogglePreview');
  const zone = document.getElementById('tfPreview');

  if (btn && zone) {
    btn.addEventListener('click', () => {
      const isVisible = window.getComputedStyle(zone).display !== 'none';

      if (isVisible) {
        zone.style.display = 'none';
      } else {
        zone.style.display = 'flex';  // hoặc 'block'
        updateFullPreview();
      }
    });
  }
}

// Khởi tạo tất cả khi DOM sẵn sàng
document.addEventListener('DOMContentLoaded', () => {
  setupRealtimePreview();
  setupPreviewToggle();
  setupFullPreviewToggle();
});
