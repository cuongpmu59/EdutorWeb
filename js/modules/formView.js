// js/modules/formView.js
import { updateLivePreview } from "./mathPreview.js";

export async function render(container) {
  const res = await fetch("mc_form_inner.php"); 
  const html = await res.text();
  container.innerHTML = html;

  // Khởi tạo các tính năng sau khi DOM đã gắn vào container
  initPostMessageListener();
  initDeleteImageButton();
  initMathPreview(); // ← Gắn sự kiện xem trước công thức

  // Optional: render lại MathJax nếu có trong nội dung HTML
  if (window.MathJax) MathJax.typesetPromise();
}

/**
 * Lắng nghe dữ liệu từ bảng (mc_table.php) gửi sang để sửa
 */
function initPostMessageListener() {
  window.addEventListener("message", (event) => {
    const data = event.data;
    if (!data || typeof data !== "object") return;

    // Gán dữ liệu vào form
    document.getElementById("mc_id").value = data.id || "";
    document.getElementById("mc_topic").value = data.topic || "";
    document.getElementById("mc_question").value = data.question || "";
    document.getElementById("mc_answer1").value = data.answer1 || "";
    document.getElementById("mc_answer2").value = data.answer2 || "";
    document.getElementById("mc_answer3").value = data.answer3 || "";
    document.getElementById("mc_answer4").value = data.answer4 || "";
    document.getElementById("mc_correct_answer").value = data.correct || "";

    const imageUrl = data.image || "";
    const imageField = document.getElementById("mc_image_url");
    const imagePreview = document.getElementById("mc_image_preview");
    const deleteBtn = document.getElementById("deleteImageBtn");

    imageField.value = imageUrl;

    if (imageUrl) {
      imagePreview.src = imageUrl;
      imagePreview.style.display = "block";
      deleteBtn.style.display = "inline-block";
    } else {
      imagePreview.style.display = "none";
      deleteBtn.style.display = "none";
    }

    // Hiện nút xoá khi đang ở chế độ sửa
    const deleteBtnMain = document.getElementById("deleteBtn");
    if (deleteBtnMain) {
      deleteBtnMain.style.display = "inline-block";
    }

    // Scroll tới form
    document.getElementById("mcForm").scrollIntoView({ behavior: "smooth" });
  });
}

/**
 * Gắn sự kiện nút xoá ảnh minh hoạ
 */
function initDeleteImageButton() {
  const deleteBtn = document.getElementById("deleteImageBtn");
  const imagePreview = document.getElementById("mc_image_preview");
  const imageUrlInput = document.getElementById("mc_image_url");

  if (deleteBtn && imagePreview && imageUrlInput) {
    deleteBtn.addEventListener("click", () => {
      const imageUrl = imageUrlInput.value;
      if (imageUrl) {
        // Gọi API xóa ảnh Cloudinary nếu có URL
        fetch("../../cloudinary/delete_cloudinary_image.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ image_url: imageUrl })
        }).then(res => res.json())
          .then(resp => {
            console.log("Image deleted:", resp);
          }).catch(err => console.error("Delete image failed:", err));
      }

      // Reset preview
      imagePreview.src = "";
      imagePreview.style.display = "none";
      imageUrlInput.value = "";
      deleteBtn.style.display = "none";
    });
  }
}

/**
 * Gắn sự kiện input để xem trước công thức Toán học trong câu hỏi
 */
function initMathPreview() {
  const textarea = document.getElementById("mc_question");
  const previewBox = document.getElementById("mc_preview_box");
  if (!textarea || !previewBox) return;

  textarea.addEventListener("input", () => {
    updateLivePreview(textarea, previewBox);
  });

  // Khởi tạo lần đầu nếu đã có nội dung
  updateLivePreview(textarea, previewBox);
}
