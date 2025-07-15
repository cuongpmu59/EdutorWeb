function renderLatex(text) {
  if (!text) return '';

  // Loại bỏ khoảng trắng đầu/cuối và escape HTML đặc biệt
  const escapeHTML = str => str.replace(/[&<>"']/g, m => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  }[m]));

  text = escapeHTML(text);

  // Ưu tiên biểu thức $$...$$ trước
  text = text.replace(/\$\$(.+?)\$\$/gs, (_, expr) => `\\[${expr.trim()}\\]`);
  text = text.replace(/\$(.+?)\$/g, (_, expr) => `\\(${expr.trim()}\\)`);

  return text;
}

function updatePreviews() {
  const fields = ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'];

  fields.forEach(id => {
    const input = document.getElementById(id);
    const preview = document.getElementById("preview_" + id);
    if (input && preview) {
      const rawText = input.value || '';
      preview.innerHTML = renderLatex(rawText);
    }
  });

  if (typeof MathJax !== 'undefined' && window.MathJax.typesetPromise) {
    MathJax.typesetPromise();
  }
}
