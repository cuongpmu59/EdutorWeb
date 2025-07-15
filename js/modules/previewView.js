// Chuyển $...$ → \( ... \) và $$...$$ → \[ ... \]
function renderLatex(text) {
  if (!text) return '';
  const inline = /\$(.+?)\$/g;
  const display = /\$\$(.+?)\$\$/g;
  return text
    .replace(display, (_, expr) => `\\[${expr}\\]`)
    .replace(inline, (_, expr) => `\\(${expr}\\)`);
}

// Cập nhật nội dung xem trước
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

// Tự co giãn textarea
function autoResizeTextarea(el) {
  el.style.height = 'auto';
  el.style.height = (el.scrollHeight) + 'px';

  if (el.scrollHeight > 200) {
    el.classList.add("small-font");
  } else {
    el.classList.remove("small-font");
  }
}

// Gán sự kiện cho các trường nhập liệu
function initPreviewListeners() {
  const fields = ['mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4'];

  fields.forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener('input', () => {
        updatePreviews();
        autoResizeTextarea(input);
      });
      autoResizeTextarea(input); // lần đầu
    }
  });
}

// Xử lý nút ẩn/hiện tất cả preview
function initTogglePreviewButton() {
  const toggleBtn = document.getElementById("togglePreviewBtn");
  if (!toggleBtn) return;

  let isShown = false;
  toggleBtn.addEventListener("click", () => {
    const boxes = document.querySelectorAll(".preview-box");
    boxes.forEach(box => {
      box.style.display = isShown ? "none" : "block";
    });
    isShown = !isShown;
    toggleBtn.textContent = isShown ? "👁️ Ẩn xem trước" : "👁️‍🗨️ Xem trước";
    if (typeof MathJax !== 'undefined') {
      MathJax.typesetPromise();
    }
  });
}

// Gọi khi trang đã sẵn sàng
document.addEventListener("DOMContentLoaded", () => {
  initPreviewListeners();
  initTogglePreviewButton();
});
