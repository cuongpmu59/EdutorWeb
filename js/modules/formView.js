// js/modules/formView.js

/**
 * Tải nội dung của form nhập câu hỏi (mc_form_inner.php)
 * và hiển thị vào phần tử container
 * @param {HTMLElement} container - phần tử DOM để hiển thị nội dung
 */
export function render(container) {
  fetch("mc_form_inner.php")
    .then(response => {
      if (!response.ok) {
        throw new Error(`Lỗi tải mc_form_inner.php: ${response.status}`);
      }
      return response.text();
    })
    .then(html => {
      container.innerHTML = html;
      renderMath(); // nếu có công thức toán
      initFormEvents(); // gắn sự kiện sau khi nội dung được load
    })
    .catch(error => {
      container.innerHTML = `<div class="error-box">❌ ${error.message}</div>`;
    });
}

/**
 * Kích hoạt MathJax nếu có công thức
 */
function renderMath() {
  if (window.MathJax && typeof MathJax.typeset === "function") {
    MathJax.typeset(); // dùng MathJax v3
  }
}

/**
 * Gắn sự kiện cần thiết sau khi nội dung được render
 */
function initFormEvents() {
  const form = document.getElementById("mcForm");
  const warningBox = document.getElementById("formWarning");

  if (!form) return;

  form.addEventListener("submit", function (e) {
    const requiredFields = ["topic", "question", "optionA", "optionB", "optionC", "optionD", "correctAnswer"];
    let isValid = true;

    for (const id of requiredFields) {
      const el = document.getElementById(id);
      if (!el || !el.value.trim()) {
        isValid = false;
        break;
      }
    }

    if (!isValid) {
      e.preventDefault();
      if (warningBox) warningBox.style.display = "block";
    } else {
      if (warningBox) warningBox.style.display = "none";
    }
  });
}
