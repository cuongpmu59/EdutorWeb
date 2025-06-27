// File: preview_module.js

let mathJaxTimer, previewTimer;

export function containsMath(content) {
  return /\\\(|\\\[|\$\$/.test(content);
}

export function debounceRenderMath(element) {
  clearTimeout(mathJaxTimer);
  mathJaxTimer = setTimeout(() => {
    if (window.MathJax && containsMath(element.innerText)) {
      MathJax.typesetPromise([element]);
    }
  }, 250);
}

export function renderPreview(fieldId) {
  const value = document.getElementById(fieldId).value;
  const previewDiv = document.getElementById("preview_" + fieldId);
  if (previewDiv) {
    previewDiv.innerHTML = value;
    debounceRenderMath(previewDiv);
    debounceFullPreview();
  }
}

export function debounceFullPreview() {
  clearTimeout(previewTimer);
  previewTimer = setTimeout(updateFullPreview, 300);
}

export function updateFullPreview() {
  const get = id => document.getElementById(id)?.value || "";
  document.getElementById("pv_id").textContent = get("question_id");
  document.getElementById("pv_topic").textContent = get("topic");
  document.getElementById("pv_question").innerHTML = get("question");
  document.getElementById("pv_a").innerHTML = get("answer1");
  document.getElementById("pv_b").innerHTML = get("answer2");
  document.getElementById("pv_c").innerHTML = get("answer3");
  document.getElementById("pv_d").innerHTML = get("answer4");
  document.getElementById("pv_correct").textContent = get("correct_answer");

  const img = document.getElementById("previewImage");
  const pvImg = document.getElementById("pv_image");
  if (img && img.src && img.style.display !== "none") {
    pvImg.src = img.src;
    pvImg.style.display = "block";
  } else {
    pvImg.src = "";
    pvImg.style.display = "none";
  }

  if (window.MathJax) {
    MathJax.typesetPromise([document.getElementById("previewBox")]);
  }
}

export function togglePreview() {
  const isChecked = document.getElementById("togglePreview")?.checked;
  document.getElementById("previewBox").style.display = isChecked ? "block" : "none";
}

export function resetPreview() {
  ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
  debounceFullPreview();
}