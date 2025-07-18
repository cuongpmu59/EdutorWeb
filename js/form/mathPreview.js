// js/modules/mathPreview.js

/**
 * Cập nhật xem trước công thức LaTeX trong 1 vùng HTML
 * Gán vào `window` để dùng trực tiếp trong <script> thường (không cần import)
 * 
 * @param {HTMLTextAreaElement} inputEl - Ô nhập LaTeX
 * @param {HTMLElement} outputEl - Vùng hiển thị kết quả
 */
window.updateLivePreview = function (inputEl, outputEl) {
    if (!inputEl || !outputEl) return;
  
    const latex = inputEl.value.trim();
  
    // Nếu không có nội dung thì làm trống kết quả
    if (!latex) {
      outputEl.innerHTML = "<em>Không có công thức để hiển thị.</em>";
      return;
    }
  
    // Đặt nội dung vào vùng hiển thị (MathJax sẽ xử lý)
    outputEl.textContent = latex;
  
    // Gọi MathJax để render lại nội dung
    if (window.MathJax && typeof MathJax.typesetPromise === "function") {
      MathJax.typesetPromise([outputEl]).catch(err => {
        outputEl.innerHTML = `<span style="color:red;">⚠️ Lỗi công thức: ${err.message}</span>`;
      });
    }
  };
  