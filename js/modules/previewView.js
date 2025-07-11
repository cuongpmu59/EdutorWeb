// js/modules/previewView.js

import { updateLivePreview } from "./mathPreview.js";

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
      renderMath(container);
      initPreviewEvents(container);
    })
    .catch(error => {
      container.innerHTML = `<div class="error-box">❌ ${error.message}</div>`;
    });
}

/**
 * Kích hoạt MathJax sau khi render
 * @param {HTMLElement} scope - vùng cần render Math
 */
function renderMath(scope = document.body) {
  if (window.MathJax && typeof MathJax.typesetPromise === "function") {
    MathJax.typesetPromise([scope]).catch(err => {
      console.error("MathJax render error:", err);
    });
  }
}

/**
 * Gắn các sự kiện sau khi xem trước được render
 * @param {HTMLElement} container
 */
function initPreviewEvents(container) {
  const refreshBtn = container.querySelector("#refreshPreviewBtn");
  if (refreshBtn) {
    refreshBtn.addEventListener("click", () => {
      render(container);
    });
  }

  // Nếu có modal ảnh
  const images = container.querySelectorAll(".preview-image");
  images.forEach(img => {
    img.addEventListener("click", () => {
      openModal(img.src);
    });
  });

  // Nếu có input live preview
  const formulaInput = container.querySelector("#previewFormulaInput");
  const previewOutput = container.querySelector("#previewFormulaOutput");
  if (formulaInput && previewOutput) {
    formulaInput.addEventListener("input", () => {
      updateLivePreview(formulaInput, previewOutput);
    });
    updateLivePreview(formulaInput, previewOutput); // Khởi tạo ban đầu
  }
}

/**
 * Hiển thị ảnh ở dạng modal
 * @param {string} src
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
  } else {
    console.warn("Modal ảnh chưa được khai báo trong DOM.");
  }
}
