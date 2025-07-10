// js/modules/imageManager.js

/**
 * Tải giao diện quản lý ảnh (mc_image.php) vào container
 * @param {HTMLElement} container - phần tử DOM để hiển thị nội dung
 */
export function render(container) {
  fetch("mc_image.php")
    .then(response => {
      if (!response.ok) {
        throw new Error(`Không thể tải mc_image.php (${response.status})`);
      }
      return response.text();
    })
    .then(html => {
      container.innerHTML = html;
      initImageEvents();
    })
    .catch(error => {
      container.innerHTML = `<div class="error-box">❌ ${error.message}</div>`;
    });
}

/**
 * Gắn sự kiện xử lý ảnh sau khi giao diện được tải
 */
function initImageEvents() {
  const imageInput = document.getElementById("imageInput");
  const previewImg = document.getElementById("imagePreview");
  const hiddenUrl = document.getElementById("image_url");
  const deleteBtn = document.getElementById("deleteImageBtn");

  // Khi chọn ảnh
  if (imageInput) {
    imageInput.addEventListener("change", async (e) => {
      const file = e.target.files[0];
      if (file) {
        try {
          const uploadedUrl = await uploadImageToCloudinary(file);
          if (hiddenUrl) hiddenUrl.value = uploadedUrl;
          if (previewImg) previewImg.src = uploadedUrl;
          previewImg.style.display = "block";
        } catch (err) {
          alert("❌ Upload ảnh thất bại: " + err.message);
        }
      }
    });
  }

  // Khi bấm xoá ảnh
  if (deleteBtn) {
    deleteBtn.addEventListener("click", async () => {
      const imageUrl = hiddenUrl?.value;
      if (imageUrl && confirm("Bạn có chắc muốn xoá ảnh này?")) {
        try {
          await deleteImageFromCloudinary(imageUrl);
          if (hiddenUrl) hiddenUrl.value = "";
          if (previewImg) {
            previewImg.src = "";
            previewImg.style.display = "none";
          }
        } catch (err) {
          alert("❌ Không thể xoá ảnh: " + err.message);
        }
      }
    });
  }
}

/**
 * Gửi ảnh lên Cloudinary
 * @param {File} file
 * @returns {Promise<string>} - URL ảnh đã upload
 */
async function uploadImageToCloudinary(file) {
  const formData = new FormData();
  formData.append("image", file);

  const response = await fetch("../../cloudinary/upload_temp_image.php", {
    method: "POST",
    body: formData
  });

  const result = await response.json();
  if (!response.ok || !result.secure_url) {
    throw new Error(result.error?.message || "Không thể upload ảnh.");
  }

  return result.secure_url;
}

/**
 * Gửi yêu cầu xoá ảnh khỏi Cloudinary
 * @param {string} imageUrl
 * @returns {Promise<void>}
 */
async function deleteImageFromCloudinary(imageUrl) {
  const response = await fetch("../../cloudinary/delete_cloudinary_image.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ image_url: imageUrl })
  });

  const result = await response.json();
  if (!response.ok || !result.success) {
    throw new Error(result.error || "Không thể xoá ảnh.");
  }
}
