// js/mc/mc_form_image.js

document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.querySelector("#mc_image");
  const previewContainer = document.querySelector("#mc_image_preview");
  const hiddenInput = document.querySelector("#mc_image_url");

  // Hàm preview ảnh khi chọn file
  function previewImage(file) {
      if (!file || !file.type.startsWith("image/")) return;

      const reader = new FileReader();
      reader.onload = function (e) {
          previewContainer.innerHTML = `
              <div class="image-item">
                  <img src="${e.target.result}" alt="Preview" />
                  <button type="button" class="btn-delete" title="Xóa ảnh">&#10006;</button>
              </div>
          `;

          // Gắn sự kiện xóa
          previewContainer.querySelector(".btn-delete").addEventListener("click", function () {
              deleteImage(hiddenInput.value);
          });
      };
      reader.readAsDataURL(file);
  }

  // Upload ảnh lên server (Cloudinary)
  function uploadImage(file) {
      let formData = new FormData();
      formData.append("file", file);
      formData.append("action", "upload");

      fetch("../../includes/mc/mc_form_image.php", {
          method: "POST",
          body: formData
      })
      .then(res => res.json())
      .then(data => {
          if (data.success) {
              hiddenInput.value = data.url;
              console.log("Ảnh đã upload:", data.url);
          } else {
              alert("Lỗi upload ảnh: " + (data.message || "Không xác định"));
              previewContainer.innerHTML = "";
          }
      })
      .catch(err => {
          console.error("Upload error:", err);
          alert("Không thể upload ảnh!");
      });
  }

  // Xóa ảnh khỏi server
  function deleteImage(url) {
      if (!url) {
          previewContainer.innerHTML = "";
          hiddenInput.value = "";
          return;
      }

      if (!confirm("Bạn có chắc muốn xóa ảnh này?")) return;

      let formData = new FormData();
      formData.append("image_url", url);
      formData.append("action", "delete");

      fetch("../../includes/mc/mc_form_image.php", {
          method: "POST",
          body: formData
      })
      .then(res => res.json())
      .then(data => {
          if (data.success) {
              previewContainer.innerHTML = "";
              hiddenInput.value = "";
              console.log("Ảnh đã xóa:", url);
          } else {
              alert("Lỗi xóa ảnh: " + (data.message || "Không xác định"));
          }
      })
      .catch(err => {
          console.error("Delete error:", err);
          alert("Không thể xóa ảnh!");
      });
  }

  // Khi người dùng chọn file
  fileInput.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
          previewImage(file);
          uploadImage(file);
      }
  });
});
