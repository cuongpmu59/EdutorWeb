document.addEventListener("DOMContentLoaded", () => {
    const fileInput = document.getElementById("imageInput");
    const preview = document.getElementById("imagePreview");
    const uploadStatus = document.getElementById("uploadStatus");
  
    fileInput.addEventListener("change", () => {
      const file = fileInput.files[0];
      if (!file) return;
  
      const formData = new FormData();
      formData.append("file", file);
      formData.append("upload_preset", "YOUR_UPLOAD_PRESET"); // Sửa nếu bạn dùng preset khác
  
      uploadStatus.innerText = "📤 Đang tải ảnh lên...";
  
      fetch("https://api.cloudinary.com/v1_1/YOUR_CLOUD_NAME/image/upload", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.secure_url) {
          localStorage.setItem("tf_image_url", data.secure_url);
          preview.src = data.secure_url;
          uploadStatus.innerText = "✅ Ảnh đã được tải lên!";
        } else {
          uploadStatus.innerText = "❌ Lỗi khi tải ảnh.";
        }
      })
      .catch(() => {
        uploadStatus.innerText = "❌ Không thể tải ảnh.";
      });
    });
  
    // Hiển thị ảnh đã lưu nếu có
    const savedUrl = localStorage.getItem("tf_image_url");
    if (savedUrl) {
      preview.src = savedUrl;
    }
  });
  