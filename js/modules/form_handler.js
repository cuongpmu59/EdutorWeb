import { $, showImagePreview } from "./dom_utils.js";

export function setupFormHandlers() {
  // Gửi form lưu hoặc cập nhật
  $("questionForm").addEventListener("submit", async function (e) {
    e.preventDefault();
    const id = $("question_id").value;
    const url = id ? "update_question.php" : "insert_question.php";
    const formData = new FormData(this);

    // Nếu là thêm mới và có ảnh temp, gửi thêm để đổi tên sau khi thêm thành công
    if (!id && $("image_url").value.includes("temp_")) {
      formData.append("temp_image", $("image_url").value);
    }

    try {
      const res = await fetch(url, {
        method: "POST",
        body: formData
      });
      const result = await res.json();
      alert(result.message || "Đã lưu!");

      // Nếu thêm mới và có ảnh tạm, đổi tên thành pic_ID
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
            showImagePreview(renameData.secure_url);
          }
        }

        // Làm mới iframe bảng câu hỏi
        $("questionIframe").contentWindow.location.reload();
      }
    } catch (err) {
      console.error("Lỗi khi gửi form:", err);
      alert("❌ Không thể gửi dữ liệu.");
    }
  });

  // Nút xoá câu hỏi
  $("deleteBtn").addEventListener("click", async () => {
    const id = $("question_id").value;
    if (!id || !confirm("Xác nhận xoá câu hỏi này?")) return;

    try {
      const res = await fetch("delete_question.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + id
      });
      const result = await res.json();
      alert(result.message || "Đã xoá!");

      if (result.success) {
        $("resetBtn").click(); // Reset form
        $("questionIframe").contentWindow.location.reload();
      }
    } catch (err) {
      console.error("Lỗi khi xoá câu hỏi:", err);
      alert("❌ Không thể xoá câu hỏi.");
    }
  });

  // Nút reset form
  $("resetBtn").addEventListener("click", () => {
    $("questionForm").reset();
    $("question_id").value = "";
    $("image_url").value = "";
    $("imageTabPreview").style.display = "none";
    $("preview_image").style.display = "none";
    $("imageTabFileName").textContent = "";
    $("delete_image_tab").style.display = "none";
  });
}
