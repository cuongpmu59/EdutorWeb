// 👉 Chuyển nội dung $...$ và $$...$$ thành \(...\) và \[...\]
function renderLatex(text) {
  if (!text) return '';
  const inline = /\$(.+?)\$/g;
  const display = /\$\$(.+?)\$\$/g;
  return text
    .replace(display, (_, expr) => `\\[${expr}\\]`)
    .replace(inline, (_, expr) => `\\(${expr}\\)`);
}

// 👉 Cập nhật toàn bộ vùng xem trước
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

// 👉 Toggle preview khi click biểu tượng 👁️
function setupTogglePreviews() {
  const fields = ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'];

  fields.forEach(id => {
    const eye = document.getElementById("eye_" + id);
    const preview = document.getElementById("preview_" + id);
    if (eye && preview) {
      eye.addEventListener("click", () => {
        preview.classList.toggle("show");
        eye.textContent = preview.classList.contains("show") ? "🙈" : "👁️";
        if (preview.classList.contains("show")) {
          updatePreviews();
        }
      });
    }
  });
}

// 👉 Gắn auto-preview khi gõ nội dung
function setupAutoPreview() {
  const fields = ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'];

  fields.forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener("input", updatePreviews);
    }
  });
}

// 👉 Gọi setup ngay khi tải trang
document.addEventListener("DOMContentLoaded", function () {
  setupTogglePreviews();
  setupAutoPreview();
  updatePreviews();
});
