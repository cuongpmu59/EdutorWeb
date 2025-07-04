
// ========== Utility ==========
const $ = id => document.getElementById(id);

function escapeHTML(str) {
  return (str || '').replace(/[&<>"]/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;'
  }[c]));
}

function processContent(raw) {
  raw = raw.trim();
  if (!/\\\(|\\\[|\\begin\{/.test(raw)) {
    return `<span>${escapeHTML(raw)}</span>`;
  }
  if (/^\\\(.+\\\)$/.test(raw) || /^\\\[.+\\\]$/.test(raw) || /^\\begin\{/.test(raw)) {
    return raw;
  }
  const parts = raw.split(/(\\\(.+?\\\)|\\\[.+?\\\]|\\begin\{[\s\S]+?\\end\{[\s\S]+?})/g);
  return parts.map(part => {
    if (/^\\\(.+\\\)$|^\\\[.+\\\]$|^\\begin\{/.test(part)) return part;
    return escapeHTML(part);
  }).join('');
}

function updateMathJax() {
  if (window.MathJax && MathJax.typesetPromise) MathJax.typesetPromise();
}

// ========== Preview Update ==========
function updatePreview() {
  const q = $("question").value;
  const a = $("answer1").value;
  const b = $("answer2").value;
  const c = $("answer3").value;
  const d = $("answer4").value;
  const correct = $("correct_answer").value;
  const showQ = $("toggle_preview_question").checked;
  const showA = $("toggle_preview_answers").checked;
  const showAll = $("toggle_preview_all").checked;

  let html = "";
  if (showAll || showQ) html += `<div><strong>Câu hỏi:</strong><br>${processContent(q)}</div><br>`;
  if (showAll || showA) {
    html += `<div>
      <strong>A:</strong> ${processContent(a)}<br>
      <strong>B:</strong> ${processContent(b)}<br>
      <strong>C:</strong> ${processContent(c)}<br>
      <strong>D:</strong> ${processContent(d)}<br>
    </div><br>`;
  }
  if (showAll) html += `<div><strong>Đáp án đúng:</strong> <span style="color:green;">${escapeHTML(correct)}</span></div>`;

  $("preview_area").innerHTML = html;
  updateMathJax();
}

// ========== Image Handling ==========
// (Omitted in this short version, assumes image handlers are unchanged)

// ========== Form Handling ==========
// (Omitted for brevity, reuse previous save/reset/delete/export logic)

// ========== Event Listeners ==========
["question", "answer1", "answer2", "answer3", "answer4", "correct_answer"].forEach(id => {
  $(id).addEventListener("input", updatePreview);
});
["toggle_preview_question", "toggle_preview_answers", "toggle_preview_all"].forEach(id => {
  $(id).addEventListener("change", updatePreview);
});

updatePreview();
