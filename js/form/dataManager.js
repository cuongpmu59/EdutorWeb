// js/modules/dataManager.js

/**
 * Lấy dữ liệu từ form câu hỏi nhiều lựa chọn
 * @returns {Object} - Dữ liệu từ form
 */
export function getFormData() {
  return {
    id: document.getElementById("question_id")?.value || "",
    topic: document.getElementById("topic")?.value.trim() || "",
    question: document.getElementById("question")?.value.trim() || "",
    answer1: document.getElementById("optionA")?.value.trim() || "",
    answer2: document.getElementById("optionB")?.value.trim() || "",
    answer3: document.getElementById("optionC")?.value.trim() || "",
    answer4: document.getElementById("optionD")?.value.trim() || "",
    correct: document.getElementById("correctAnswer")?.value || "",
    image: document.getElementById("image_url")?.value || "",
  };
}

/**
 * Gán dữ liệu vào form (dùng khi sửa)
 * @param {Object} data - Dữ liệu câu hỏi
 */
export function setFormData(data = {}) {
  document.getElementById("question_id").value = data.id || "";
  document.getElementById("topic").value = data.topic || "";
  document.getElementById("question").value = data.question || "";
  document.getElementById("optionA").value = data.answer1 || "";
  document.getElementById("optionB").value = data.answer2 || "";
  document.getElementById("optionC").value = data.answer3 || "";
  document.getElementById("optionD").value = data.answer4 || "";
  document.getElementById("correctAnswer").value = data.correct || "";
  if (document.getElementById("image_url")) {
    document.getElementById("image_url").value = data.image || "";
  }
}

/**
 * Xoá toàn bộ dữ liệu trên form
 */
export function resetForm() {
  document.getElementById("mcForm")?.reset();
  document.getElementById("question_id").value = "";
  document.getElementById("formWarning")?.style?.setProperty("display", "none");
}

/**
 * Kiểm tra form hợp lệ (có đủ dữ liệu chưa)
 * @returns {boolean}
 */
export function isFormValid() {
  const fields = ["topic", "question", "optionA", "optionB", "optionC", "optionD", "correctAnswer"];
  for (const id of fields) {
    const el = document.getElementById(id);
    if (!el || !el.value.trim()) {
      return false;
    }
  }
  return true;
}

/**
 * Chuyển dữ liệu sang định dạng dùng cho export Excel hoặc preview
 * @param {Object} data
 * @returns {Object}
 */
export function formatForExport(data) {
  return {
    Chủ_đề: data.topic,
    Câu_hỏi: data.question,
    A: data.answer1,
    B: data.answer2,
    C: data.answer3,
    D: data.answer4,
    Đáp_án_đúng: data.correct,
    Ảnh: data.image || "",
  };
}
