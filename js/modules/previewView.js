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
      const rawText = input.value;
      preview.innerHTML = renderLatex(rawText);
    }
  });

  if (typeof MathJax !== 'undefined') {
    MathJax.typesetPromise();
  }
}
