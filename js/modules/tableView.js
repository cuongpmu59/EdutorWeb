// js/modules/tableView.js

/**
 * Tải nội dung bảng danh sách câu hỏi từ mc_table.php
 * @param {HTMLElement} container - vùng hiển thị
 */
export function render(container) {
  fetch("mc_table.php")
    .then(response => {
      if (!response.ok) {
        throw new Error(`Không thể tải mc_table.php (${response.status})`);
      }
      return response.text();
    })
    .then(html => {
      container.innerHTML = html;
      initTableEvents();
      renderMath();
    })
    .catch(error => {
      container.innerHTML = `<div class="error-box">❌ ${error.message}</div>`;
    });
}

/**
 * Khởi tạo các sự kiện trên bảng dữ liệu
 */
function initTableEvents() {
  const rows = document.querySelectorAll(".question-row");
  rows.forEach(row => {
    row.addEventListener("click", () => {
      const data = row.dataset;
      // Gửi dữ liệu câu hỏi cho form để sửa
      window.parent.postMessage({
        id: data.id,
        topic: data.topic,
        question: data.question,
        answer1: data.answer1,
        answer2: data.answer2,
        answer3: data.answer3,
        answer4: data.answer4,
        correct: data.correct,
        image: data.image
      }, "*");
    });
  });

  // Gắn sự kiện modal ảnh nếu cần
  const images = document.querySelectorAll(".table-image");
  images.forEach(img => {
    img.addEventListener("click", () => {
      openModal(img.src);
    });
  });
}

/**
 * Kích hoạt MathJax nếu có công thức toán
 */
function renderMath() {
  if (window.MathJax && typeof MathJax.typeset === "function") {
    MathJax.typeset();
  }
}

/**
 * Hiển thị ảnh modal
 */
function openModal(src) {
  const modal = document.getElementById("imageModal");
  const modalImg = document.getElementById("modalImage");
  if (modal && modalImg) {
    modalImg.src = src;
    modal.style.display = "block";

    modal.addEventListener("click", () => {
      modal.style.display = "none";
      modalImg.src = "";
    });
  }
}
