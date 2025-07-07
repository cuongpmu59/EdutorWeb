import { $, updatePreview } from "./preview.js";

export function setupIframeListener() {
  window.addEventListener("message", (event) => {
    const data = event.data;
    if (!data || typeof data !== "object" || data.type !== "fillForm") return;

    // Gán dữ liệu vào form
    $("question_id").value = data.id;
    $("topic").value = data.topic || "";
    $("question").value = data.question || "";
    $("answer1").value = data.answer1 || "";
    $("answer2").value = data.answer2 || "";
    $("answer3").value = data.answer3 || "";
    $("answer4").value = data.answer4 || "";
    $("correct_answer").value = data.correct_answer || "";
    $("image_url").value = data.image || "";

    // Hiển thị ảnh
    if (data.image) {
      $("imageTabPreview").src = data.image;
      $("imageTabPreview").style.display = "block";
      $("imageTabFileName").textContent = "Đã có ảnh";
      $("delete_image_tab").style.display = "inline-block";

      $("preview_image").src = data.image;
      $("preview_image").style.display = "block";
    } else {
      $("imageTabPreview").style.display = "none";
      $("imageTabFileName").textContent = "";
      $("delete_image_tab").style.display = "none";
      $("preview_image").style.display = "none";
    }

    updatePreview();
  });
}
