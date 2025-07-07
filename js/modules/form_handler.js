import { $ } from "./dom_utils.js";
import { updatePreview } from "./preview.js";

export function setupFormHandlers() {
  $("resetBtn").addEventListener("click", () => {
    $("questionForm").reset();
    $("question_id").value = "";
    $("image_url").value = "";
    $("imageTabPreview").style.display = "none";
    $("preview_image").style.display = "none";
    $("imageTabFileName").textContent = "";
    $("delete_image_tab").style.display = "none";
    updatePreview();
  });

  $("questionForm").addEventListener("submit", async function (e) {
    e.preventDefault();
    const id = $("question_id").value;
    const url = id ? "update_question.php" : "insert_question.php";
    const formData = new FormData(this);

    if (!id && $("image_url").value.includes("temp_")) {
      formData.append("temp_image", $("image_url").value);
    }

    const res = await fetch(url, { method: "POST", body: formData });
    const result = await res.json();
    alert(result.message || "Đã lưu!");

    if (result.success) {
      if (result.new_id && $("image_url").value.includes("temp_")) {
        const renameRes = await fetch("rename_cloudinary_image.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `old_url=${encodeURIComponent($("image_url").value)}&new_name=pic_${result.new_id}`
        });

        const renameData = await renameRes.json();
        if (renameData.secure_url) {
          $("image_url").value = renameData.secure_url;
          $("imageTabPreview").src = renameData.secure_url;
          $("preview_image").src = renameData.secure_url;
        }
      }

      document.getElementById("questionIframe").contentWindow.location.reload();
    }
  });

  $("deleteBtn").addEventListener("click", async () => {
    const id = $("question_id").value;
    if (!id || !confirm("Xác nhận xoá câu hỏi này?")) return;

    const res = await fetch("delete_question.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "id=" + id
    });

    const result = await res.json();
    alert(result.message || "Đã xoá!");

    if (result.success) {
      $("resetBtn").click();
      document.getElementById("questionIframe").contentWindow.location.reload();
    }
  });
}
