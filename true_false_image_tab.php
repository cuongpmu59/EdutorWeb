<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Ảnh minh hoạ</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }

    input[type="file"] {
      margin-top: 10px;
    }

    #preview {
      margin-top: 20px;
      max-width: 100%;
      border: 1px solid #ccc;
      padding: 8px;
      background-color: #f9f9f9;
    }

    #preview img {
      max-width: 100%;
      height: auto;
    }

    .loading {
      color: orange;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <h2>🖼️ Ảnh minh hoạ cho câu hỏi</h2>

  <input type="file" id="imageInput" accept="image/*"><br>
  <div id="status" class="loading"></div>
  <div id="preview"></div>

  <script>
    const imageInput = document.getElementById("imageInput");
    const status = document.getElementById("status");
    const preview = document.getElementById("preview");

    imageInput.addEventListener("change", async function () {
      const file = this.files[0];
      if (!file) return;

      status.textContent = "⏳ Đang tải ảnh lên...";
      preview.innerHTML = "";

      const formData = new FormData();
      formData.append("file", file);
      formData.append("upload_preset", "<?php echo getenv('CLOUDINARY_UPLOAD_PRESET'); ?>");

      try {
        const response = await fetch("https://api.cloudinary.com/v1_1/<?php echo getenv('CLOUDINARY_CLOUD_NAME'); ?>/image/upload", {
          method: "POST",
          body: formData
        });

        const data = await response.json();

        if (data.secure_url) {
          const imageUrl = data.secure_url;

          // Lưu vào localStorage để các tab khác truy cập
          localStorage.setItem("true_false_image_url", imageUrl);

          status.textContent = "✅ Tải ảnh thành công";
          preview.innerHTML = `<img src="${imageUrl}" alt="Preview">`;
        } else {
          status.textContent = "❌ Lỗi khi tải ảnh lên Cloudinary.";
        }

      } catch (error) {
        console.error(error);
        status.textContent = "❌ Có lỗi xảy ra khi upload.";
      }
    });

    // Hiển thị lại ảnh nếu đã có trong localStorage
    window.addEventListener("DOMContentLoaded", () => {
      const savedUrl = localStorage.getItem("true_false_image_url");
      if (savedUrl) {
        preview.innerHTML = `<img src="${savedUrl}" alt="Preview">`;
        status.textContent = "📌 Ảnh đã được chọn trước đó";
      }
    });
  </script>
</body>
</html>
