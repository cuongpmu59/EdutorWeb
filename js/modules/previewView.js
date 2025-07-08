// previewView.js

export function renderPreview(prefix = "mc") {
  const previewBox = document.getElementById(`${prefix}_preview_content`);
  if (!previewBox) return;

  let html = "";

  if (prefix === "mc") {
    const question = getValue(`${prefix}_question`);
    const a1 = getValue(`${prefix}_answer1`);
    const a2 = getValue(`${prefix}_answer2`);
    const a3 = getValue(`${prefix}_answer3`);
    const a4 = getValue(`${prefix}_answer4`);
    html = `
      <p><strong>📘 Câu hỏi:</strong> ${question}</p>
      <ul>
        <li>A. ${a1}</li>
        <li>B. ${a2}</li>
        <li>C. ${a3}</li>
        <li>D. ${a4}</li>
      </ul>
    `;

  } else if (prefix === "tf") {
    const main = getValue(`${prefix}_question`);
    const s1 = getValue(`${prefix}_statement1`);
    const s2 = getValue(`${prefix}_statement2`);
    const s3 = getValue(`${prefix}_statement3`);
    const s4 = getValue(`${prefix}_statement4`);
    html = `
      <p><strong>🧠 Câu hỏi:</strong> ${main}</p>
      <ol>
        <li>1. ${s1}</li>
        <li>2. ${s2}</li>
        <li>3. ${s3}</li>
        <li>4. ${s4}</li>
      </ol>
    `;

  } else if (prefix === "sa") {
    const question = getValue(`${prefix}_question`);
    const b = getValue(`${prefix}_correct_answer`);
    html = `
      <p><strong>📝 Câu hỏi:</strong> ${question}</p>
      <ol><li>1. ${b}</li></ol>
    `;
  }

  previewBox.innerHTML = html;

  // Kích hoạt lại MathJax để hiển thị công thức
  if (window.MathJax) MathJax.typesetPromise?.();
}

function getValue(id) {
  const el = document.getElementById(id);
  return el ? el.value : "";
}
