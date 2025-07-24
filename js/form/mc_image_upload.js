document.addEventListener("DOMContentLoaded", function () {
    const imageInput = document.getElementById("imageInput");
    const imageUrlInput = document.getElementById("imageUrl");
    const imagePreview = document.getElementById("imagePreview");
  
    const cloudName = "your_cloud_name";       // ← thay bằng tên Cloudinary của bạn
    const uploadPreset = "mc_unsigned";         // ← tên upload preset unsigned
  
    imageInput.addEventListener("change", function () {
      const file = imageInput.files[0];
      if (!file) return;
  
      const formData = new FormData();
      formData.append("file", file);
      formData.append("upload_preset", uploadPreset);
  
      // Gửi yêu cầu đến Cloudinary
      fetch(`https://api.cloudinary.com/v1_1/${cloudName}/image/upload`, {
        method: "POST",
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.secure_url) {
          imageUrlInput.value = data.secure_url;
          imagePreview.src = data.secure_url;
          imagePreview.style.display = "block";
        } else {
          alert("Upload ảnh thất bại!");
          console.error(data);
        }
      })
      .catch(error => {
        alert("Lỗi khi upload ảnh!");
        console.error(error);
      });
    });
  });
  