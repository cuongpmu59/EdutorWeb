// 🔁 Chuyển $...$ và $$...$$ thành \(...\) và \[...\]
function renderLatex(text) {
  if (!text) return '';
  const inline = /\$(.+?)\$/g;
  const display = /\$\$(.+?)\$\$/g;
  return text
    .replace(display, (_, expr) => `\\[${expr}\\]`)
    .replace(inline, (_, expr) => `\\(${expr}\\)`);
}

// 🚀 Cập nhật toàn bộ vùng xem trước
function updatePreviews() {
  const fields = ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'];

  fields.forEach(id => {
    const input = document.getElementById(id);
    const preview = document.getElementById("preview_" + id);
    if (input && preview) {
      const rawText = input.value;
      preview.innerHTML = renderLatex(rawText);
    }
  });

  if (typeof MathJax !== 'undefined') {
    MathJax.typesetPromise();
  }
}

// 👁️ Xử lý toggle preview khi nhấn biểu tượng
function setupTogglePreviews() {
  const fields = ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'];

  fields.forEach(id => {
    const eyeIcon = document.getElementById("eye_" + id);
    const previewBox = document.getElementById("preview_" + id);

    if (eyeIcon && previewBox) {
      eyeIcon.addEventListener("click", () => {
        const isShown = previewBox.classList.toggle("show");
        eyeIcon.textContent = isShown ? "🙈" : "👁️";

        if (isShown) updatePreviews();
      });
    }
  });
}

// 📝 Tự động update khi gõ nội dung
function setupAutoPreview() {
  const fields = ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'];

  fields.forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener("input", updatePreviews);
    }
  });
}

// 🚦 Khởi tạo khi DOM đã sẵn sàng
document.addEventListener("DOMContentLoaded", () => {
  setupTogglePreviews();
  setupAutoPreview();
  updatePreviews();
});
