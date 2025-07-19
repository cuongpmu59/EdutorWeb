const fieldIds = ["mc_question", "mc_answer1", "mc_answer2", "mc_answer3", "mc_answer4"];

fieldIds.forEach(id => {
  const input = document.getElementById(id);
  if (input) {
    input.addEventListener("input", () => {
      if (typeof updatePreviews === "function") updatePreviews();
    });
  }

  // Gắn sự kiện toggle preview 👁️
  const eye = document.getElementById(`eye_${id}`);
  const preview = document.getElementById(`preview_${id}`);
  if (eye && preview) {
    eye.style.cursor = "pointer";
    eye.addEventListener("click", () => {
      preview.style.display = (preview.style.display === "none" || !preview.style.display)
        ? "block" : "none";
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("toggleIframeBtn");
  const iframe = document.getElementById("mcIframe");

  toggleBtn.addEventListener("click", function () {
    const isVisible = iframe.classList.toggle("show");
    toggleBtn.textContent = isVisible ? "🔽 Ẩn bảng câu hỏi" : "🔼 Hiện bảng câu hỏi";
  });
});
