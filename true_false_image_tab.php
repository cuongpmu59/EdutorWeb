<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🖼️ Ảnh minh hoạ cho câu hỏi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/true_false_image_tab.css">
</head>
<body>
  <div class="image-tab-container">
    <h2>🖼️ Ảnh minh hoạ cho câu hỏi</h2>

    <div class="button-group">
      <label for="imageInput" class="btn-select">📁 Chọn ảnh</label>
      <input type="file" id="imageInput" accept="image/*" hidden>
      <button id="deleteImageBtn" class="btn-delete" style="display: none;">🗑️ Xoá ảnh</button>
    </div>

    <div id="status" class="status loading"></div>
    <div id="preview" class="preview-box"></div>
  </div>

  <script>
    const imageInput = document.getElementById("imageInput");
    const status = document.getElementById("status");
    const preview = document.getElementById("preview");
    const deleteBtn = document.getElementById("deleteImageBtn");

    // Sự kiện khi chọn ảnh mới
    imageInput.addEventListener("change", async function () {
      const file = this.files[0];
      if (!file) return;

      status.textContent = "⏳ Đang tải ảnh lên...";
      status.className = "status loading";
      preview.innerHTML = "";
      deleteBtn.style.display = "none";

      const formData = new FormData();
      formData.append("file", file);

      try {
        const response = await fetch("upload_image.php", {
          method: "POST",
          body: formData
        });

        const data = await response.json();
        if (data.success && data.secure_url && data.public_id) {
          const imageUrl = data.secure_url;
          const publicId = data.public_id;

          // Lưu vào localStorage để dùng trong form chính
          localStorage.setItem("true_false_image_url", imageUrl);
          localStorage.setItem("true_false_image_public_id", publicId);

          status.textContent = "✅ Tải ảnh thành công!";
          status.className = "status success";
          preview.innerHTML = `<img src="${imageUrl}" alt="Ảnh minh hoạ">`;
          deleteBtn.style.display = "inline-block";
        } else {
          status.textContent = "❌ Lỗi khi tải ảnh.";
          status.className = "status error";
        }
      } catch (err) {
        console.error("Upload error:", err);
        status.textContent = "❌ Lỗi kết nối khi upload.";
        status.className = "status error";
      }
    });

    // Xoá ảnh khỏi Cloudinary
    deleteBtn.addEventListener("click", async () => {
      const publicId = localStorage.getItem("true_false_image_public_id");
      const imageUrl = localStorage.getItem("true_false_image_url");
      if (!publicId) return;

      const confirmDelete = confirm("Bạn có chắc muốn xoá ảnh khỏi Cloudinary?");
      if (!confirmDelete) return;

      try {
        const response = await fetch("delete_cloudinary_image.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ public_id: publicId })
        });

        const result = await response.json();
        if (result.success) {
          localStorage.removeItem("true_false_image_url");
          localStorage.removeItem("true_false_image_public_id");
          preview.innerHTML = "";
          deleteBtn.style.display = "none";
          status.textContent = "🗑️ Đã xoá ảnh thành công.";
          status.className = "status success";
        } else {
          status.textContent = "❌ Không thể xoá ảnh: " + result.message;
          status.className = "status error";
        }
      } catch (err) {
        console.error("Delete error:", err);
        status.textContent = "❌ Lỗi khi gửi yêu cầu xoá.";
        status.className = "status error";
      }
    });

    // Tải ảnh cũ nếu đã có lưu
    window.addEventListener("DOMContentLoaded", () => {
      const url = localStorage.getItem("true_false_image_url");
      const publicId = localStorage.getItem("true_false_image_public_id");
      if (url && publicId) {
        preview.innerHTML = `<img src="${url}" alt="Ảnh minh hoạ">`;
        status.textContent = "📌 Ảnh đã được chọn trước đó.";
        status.className = "status success";
        deleteBtn.style.display = "inline-block";
      }
    });
  </script>
</body>
</html>
