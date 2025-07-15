function renderLatex(text) {
  if (!text) return '';
  const inline = /\$(.+?)\$/g;
  const display = /\$\$(.+?)\$\$/g;
  return text
    .replace(display, (_, expr) => `\\[${expr}\\]`)
    .replace(inline, (_, expr) => `\\(${expr}\\)`);
}

function updatePreviews() {
  const fields = ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'];
  fields.forEach(id => {
    const input = document.getElementById(id);
    const preview = document.getElementById("preview_" + id);
    if (input && preview) {
      preview.innerHTML = renderLatex(input.value);
    }
  });
  if (typeof MathJax !== 'undefined') MathJax.typesetPromise();
}

function setupTogglePreviews() {
  document.querySelectorAll(".toggle-preview").forEach(icon => {
    const targetId = icon.dataset.target;
    const previewBox = document.getElementById(targetId);
    icon.addEventListener("click", () => {
      previewBox.classList.toggle("show");
      icon.textContent = previewBox.classList.contains("show") ? "🙈" : "👁️";
      if (previewBox.classList.contains("show")) updatePreviews();
    });
  });
}

function setupAutoPreview() {
  const fields = ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'];
  fields.forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener("input", updatePreviews);
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  setupTogglePreviews();
  setupAutoPreview();
  updatePreviews();
});
