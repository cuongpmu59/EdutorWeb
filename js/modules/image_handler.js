import { $, showImagePreview } from "./dom_utils.js";

// ⚙️ Cấu hình Cloudinary (phải gán giá trị từ biến toàn cục hoặc .env chuyển qua <script>)
const CLOUDINARY_CLOUD_NAME = window.CLOUDINARY_CLOUD_NAME;
const CLOUDINARY_UPLOAD_PRESET = window.CLOUDINARY_UPLOAD_PRESET;

export function setupImageHandlers() {
  // Chọn ảnh từ tab "Ảnh minh hoạ"
  $("select_image_tab").addEventListener("click", () => {
    $("image").click();
  });

  // Khi người dùng chọn ảnh => upload lên Cloudinary
  $("image").addEventListener("change", async function () {
    const file = this.files[0];
    if (!file) return;

    const formData = new FormData();
    const tempName = "temp_" + Date.now();
    formData.append("file", file);
    formData.append("upload_preset", CLOUDINARY_UPLOAD_PRESET);
    formData.append("public_id", tempName);

    try {
      const res = await fetch(`https://api.cloudinary.com/v1_1/${CLOUDINARY_CLOUD_NAME}/image/upload`, {
        method: "POST",
        body: formData
      });

      const data = await res.json();
      if (data.secure_url) {
        $("image_url").value = data.secure_url;
        showImagePreview(data.secure_url, file.name);
      } else {
        alert("❌ Upload ảnh thất bại.");
      }
    } catch (err) {
      console.error("Lỗi upload ảnh:", err);
      alert("❌ Không thể upload ảnh.");
    }
  });

  // Nút xoá ảnh
  $("delete_image_tab").addEventListener("click", async () => {
    const url = $("image_url").value;
    if (!url) return;

    if (!confirm("Bạn có chắc muốn xoá ảnh này không?")) return;

    try {
      await fetch("delete_cloudinary_image.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "url=" + encodeURIComponent(url)
      });
    } catch (err) {
      console.error("Lỗi khi gửi yêu cầu xoá ảnh:", err);
    }

    // Xoá ảnh khỏi form
    $("image_url").value = "";
    $("imageTabPreview").style.display = "none";
    $("preview_image").style.display = "none";
    $("imageTabFileName").textContent = "";
    $("delete_image_tab").style.display = "none";
  });
}
