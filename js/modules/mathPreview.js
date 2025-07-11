// js/modules/mathPreview.js

/**
 * Gọi MathJax để render lại toàn bộ hoặc vùng cụ thể.
 * @param {HTMLElement} [container=document.body] - Phần tử cần render MathJax.
 */
export function renderMath(container = document.body) {
    if (window.MathJax) {
      MathJax.typesetPromise([container]);
    } else {
      console.warn("⚠️ MathJax chưa được tải.");
    }
  }
  
  /**
   * Tạo bản xem trước công thức từ input text, hỗ trợ LaTeX giữa \( ... \) hoặc \[ ... \]
   * @param {string} input - Chuỗi văn bản có chứa công thức LaTeX
   * @returns {string} - Chuỗi HTML đã format sẵn
   */
  export function formatMathPreview(input) {
    // Escape HTML đơn giản (tránh <script>)
    const escaped = input
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
  
    // Có thể thêm logic highlight công thức ở đây nếu muốn
  
    return escaped;
  }
  
  /**
   * Cập nhật bản xem trước công thức từ ô nhập liệu vào vùng preview
   * @param {HTMLTextAreaElement} textarea - Vùng nhập
   * @param {HTMLElement} previewBox - Vùng hiển thị kết quả
   */
  export function updateLivePreview(textarea, previewBox) {
    if (!textarea || !previewBox) return;
    const raw = textarea.value || "";
    const html = formatMathPreview(raw);
  
    previewBox.innerHTML = html;
    renderMath(previewBox);
  }
  