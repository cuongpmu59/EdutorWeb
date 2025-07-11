import { updateLivePreview } from "./mathPreview.js";

/**
 * Load giao diện form từ mc_form_inner.php và khởi tạo sự kiện
 */
export async function render(container) {
  const res = await fetch("pages/mc/mc_form_inner.php");
  const html = await res.text();
  container.innerHTML = html;

  initPostMessageListener();
  initDeleteImageButton();
  initMathPreview();
  initFormValidation();

  // Render MathJax nếu có
  if (window.MathJax) MathJax.typesetPromise();
}

/**
 * Nhận dữ liệu từ bảng mc_table gửi sang form để sửa
 */
function initPostMessageListener() {
  window.addEventListener("message", (event) => {
    const data = event.data;
    if (!data || typeof data !== "object") return;

    document.getElementById("mc_id").value = data.id || "";
    document.getElementById("mc_topic").value = data.topic || "";
    document.getElementById("mc_question").value = data.question || "";
    document.getElementById("mc_answer1").value = data.answer1 || "";
    document.getElementById("mc_answer2").value = data.answer2 || "";
    document.getElementById("mc_answer3").value = data.answer3 || "";
    document.getElementById("mc_answer4").value = data.answer4 || "";
    document.getElementById("mc_correct_answer").value = data.correct || "";

    const imageField = document.getElementById("mc_image_url");
    const imagePreview = document.getElementById("mc_image_preview");
    const deleteBtn = document.getElementById("deleteImageBtn");

    if (imageField) imageField.value = data.image || "";

    if (data.image) {
      if (imagePreview) {
        imagePreview.src = data.image;
        imagePreview.style.display = "block";
      }
      if (deleteBtn) deleteBtn.style.display = "inline-block";
    } else {
      if (imagePreview) imagePreview.style.display = "none";
      if (deleteBtn) deleteBtn.style.display = "none";
    }

    const deleteBtnMain = document.getElementById("deleteBtn");
    if (deleteBtnMain) deleteBtnMain.style.display = "inline-block";

    document.getElementById("mcForm")?.scrollIntoView({ behavior: "smooth" });
  });
}

/**
 * Gắn sự kiện xoá ảnh minh hoạ từ Cloudinary
 */
function initDeleteImageButton() {
  const deleteBtn = document.getElementById("deleteImageBtn");
  const imagePreview = document.getElementById("mc_image_preview");
  const imageUrlInput = document.getElementById("mc_image_url");

  if (deleteBtn && imagePreview && imageUrlInput) {
    deleteBtn.addEventListener("click", () => {
      const imageUrl = imageUrlInput.value;
      if (imageUrl) {
        fetch("../../cloudinary/delete_cloudinary_image.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ image_url: imageUrl })
        }).then(res => res.json())
          .then(resp => console.log("Image deleted:", resp))
          .catch(err => console.error("Delete failed:", err));
      }

      imagePreview.src = "";
      imagePreview.style.display = "none";
      imageUrlInput.value = "";
      deleteBtn.style.display = "none";
    });
  }
}

/**
 * Gắn sự kiện nhập công thức để xem trước
 */
function initMathPreview() {
  const input = document.getElementById("previewFormulaInput");
  const output = document.getElementById("previewFormulaOutput");

  if (input && output) {
    input.addEventListener("input", () => {
      updateLivePreview(input, output);
    });

    // Render lần đầu nếu có nội dung
    updateLivePreview(input, output);
  }
}

/**
 * Kiểm tra dữ liệu form trước khi submit
 */
function initFormValidation() {
  const form = document.getElementById("mcForm");
  const warning = document.getElementById("formWarning");

  if (form) {
    form.addEventListener("submit", function (e) {
      const fields = [
        "mc_topic", "mc_question", "mc_answer1", "mc_answer2",
        "mc_answer3", "mc_answer4", "mc_correct_answer"
      ];
      let valid = true;
      for (const id of fields) {
        const el = document.getElementById(id);
        if (!el || !el.value.trim()) {
          valid = false;
          break;
        }
      }

      if (!valid) {
        e.preventDefault();
        if (warning) warning.style.display = "block";
      } else {
        if (warning) warning.style.display = "none";
      }
    });
  }
}
