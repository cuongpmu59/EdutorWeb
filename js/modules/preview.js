import { $ } from "./dom_utils.js";

/**
 * Cập nhật khối xem trước với công thức MathJax
 */
export function updatePreview() {
  const content = `
    <strong>Chủ đề:</strong> ${$("topic").value}<br>
    <strong>Câu hỏi:</strong><br> ${$("question").value}<br>
    <strong>Đáp án:</strong><br>
    A. ${$("answer1").value}<br>
    B. ${$("answer2").value}<br>
    C. ${$("answer3").value}<br>
    D. ${$("answer4").value}
  `;
  $("preview_area").innerHTML = content;
  MathJax.typesetPromise?.();
}

/**
 * Thiết lập lắng nghe các input để tự động cập nhật xem trước
 */
export function setupLivePreview() {
  ["topic", "question", "answer1", "answer2", "answer3", "answer4"].forEach(id => {
    $(id).addEventListener("input", updatePreview);
  });
}
