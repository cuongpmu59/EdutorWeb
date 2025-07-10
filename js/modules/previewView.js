// js/modules/previewView.js

/**
 * Tải nội dung xem trước từ mc_preview.php
 * và hiển thị vào vùng container
 * @param {HTMLElement} container
 */
export function render(container) {
  fetch("mc_preview.php")
    .then(response => {
      if (!response.ok) {
        throw new Error(`Không thể tải xem trước (${response.status})`);
      }
      return response.text();
    })
    .then(html => {
      container.innerHTML = html;
      renderMath();
      initPreviewEvents();
    })
    .catch(error => {
      container.innerHTML = `<div class="error-box">❌ ${error.message}</div>`;
    });
}

/**
 * Kích hoạt MathJax sau khi render
 */
function renderMath() {
  if (window.MathJax && typeof MathJax.typeset === "function") {
    MathJax.typeset();
  }
}

/**
 * Gắn các sự kiện sau khi xem trước được render
 */
function initPreviewEvents() {
  const refreshBtn = document.getElementById("refreshPreviewBtn");
  if (refreshBtn) {
    refreshBtn.addEventListener("click", () => {
      render(document.getElementById("tabContent"));
    });
  }

  // Nếu bạn có modal ảnh hoặc preview nâng cao, khởi động ở đây
  const images = document.querySelectorAll(".preview-image");
  images.forEach(img => {
    img.addEventListener("click", () => {
      openModal(img.src);
    });
  });
}

/**
 * Hiển thị ảnh ở dạng modal (nếu có chức năng này)
 * @param {string} src - URL ảnh
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
