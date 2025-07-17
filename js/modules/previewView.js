document.addEventListener("DOMContentLoaded", () => {
  const fields = [
    "mc_question",
    "mc_answer1",
    "mc_answer2",
    "mc_answer3",
    "mc_answer4"
  ];

  fields.forEach((id) => {
    const input = document.getElementById(id);
    const preview = document.getElementById("preview_" + id);
    const eyeBtn = document.getElementById("eye_" + id);

    // Toggle xem trước
    eyeBtn.addEventListener("click", () => {
      if (preview.style.display === "block") {
        preview.style.display = "none";
        eyeBtn.textContent = "👁️";
      } else {
        preview.style.display = "block";
        eyeBtn.textContent = "🙈";
        updatePreview(id);
      }
    });

    // Cập nhật nội dung realtime
    input.addEventListener("input", () => {
      if (preview.style.display === "block") {
        updatePreview(id);
      }
    });
  });
});

// Hàm cập nhật 1 trường cụ thể
function updatePreview(id) {
  const input = document.getElementById(id);
  const preview = document.getElementById("preview_" + id);
  const content = input.value;

  // Chuyển đổi nội dung hiển thị (text + công thức)
  preview.innerText = content;

  // Kích hoạt MathJax nếu có công thức
  if (window.MathJax) {
    MathJax.typesetPromise([preview]).catch((err) =>
      console.error("MathJax render error:", err)
    );
  }
}

// Cập nhật tất cả khi load form từ bảng
function updatePreviews() {
  const fields = [
    "mc_question",
    "mc_answer1",
    "mc_answer2",
    "mc_answer3",
    "mc_answer4"
  ];

  fields.forEach((id) => {
    const preview = document.getElementById("preview_" + id);
    if (preview && preview.style.display === "block") {
      updatePreview(id);
    }
  });
}
