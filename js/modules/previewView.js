document.addEventListener("DOMContentLoaded", () => {
  const fields = [
    "mc_question",
    "mc_answer1",
    "mc_answer2",
    "mc_answer3",
    "mc_answer4"
  ];

  function renderLatexWithText(text) {
    if (!text) return "";

    // Escape HTML
    let escaped = text
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");

    // Replace $$...$$ with MathJax inline math
    escaped = escaped.replace(/\$\$(.*?)\$\$/g, (_, expr) => {
      return `<span class="math-tex">\\(${expr}\\)</span>`;
    });

    return escaped;
  }

  function updatePreviews() {
    fields.forEach(field => {
      const input = document.getElementById(field);
      const preview = document.getElementById("preview_" + field);
      if (input && preview) {
        preview.innerHTML = renderLatexWithText(input.value);
      }
    });

    if (window.MathJax && typeof MathJax.typesetPromise === "function") {
      MathJax.typesetPromise();
    }
  }

  // Gán sự kiện input để cập nhật preview real-time
  fields.forEach(field => {
    const el = document.getElementById(field);
    if (el) el.addEventListener("input", updatePreviews);
  });

  // Gắn hàm toàn cục để dùng từ bên ngoài
  window.updatePreviews = updatePreviews;

  // Gọi khi trang tải xong (nếu dữ liệu đã đổ từ form cha)
  updatePreviews();
});
