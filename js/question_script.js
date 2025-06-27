// question_script.js
import {
  renderPreview,
  resetPreview,
  debounceFullPreview,
  togglePreview
} from './preview_module.js';

// ========== 1. Utility Functions ==========
function getFormData() {
  return new FormData(document.getElementById("questionForm"));
}

function refreshIframe() {
  const iframe = document.getElementById("questionIframe");
  if (iframe) {
    iframe.contentWindow.location.reload();
    iframe.onload = function () {
      if (iframe.contentWindow.MathJax) {
        iframe.contentWindow.MathJax.typesetPromise();
      }
    };
  }
}

// ========== 3. Save ==========
async function saveQuestion() {
  const id = document.getElementById("question_id").value.trim();
  const formData = getFormData();
  formData.set("delete_image", document.getElementById("delete_image").checked ? "1" : "0");

  const required = ["question", "answer1", "answer2", "answer3", "answer4", "correct_answer", "topic"];
  for (let field of required) {
    if (!formData.get(field)?.trim()) {
      alert("Vui lòng điền đầy đủ thông tin.");
      return;
    }
  }

  const imageFile = formData.get("image");
  if (imageFile && imageFile.size > 0) {
    if (!imageFile.type.startsWith("image/")) {
      alert("Chỉ chấp nhận file ảnh!");
      return;
    }
    if (imageFile.size > 2 * 1024 * 1024) {
      alert("Ảnh vượt quá 2MB.");
      return;
    }
  }

  const saveBtn = document.querySelector(".form-right button:nth-child(1)");
  if (saveBtn.disabled) return; // ngăn double click
  saveBtn.disabled = true;

  if (imageFile && imageFile.size > 0) {
    const cloudForm = new FormData();
    cloudForm.append("file", imageFile);
    cloudForm.append("upload_preset", "quiz_photo");

    try {
      const res = await fetch("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/upload", {
        method: "POST",
        body: cloudForm,
      });
      const data = await res.json();
      if (data.secure_url) formData.set("image_url", data.secure_url);
    } catch (err) {
      alert("Không thể tải ảnh lên Cloudinary: " + err.message);
      saveBtn.disabled = false;
      return;
    }
  }

  const apiUrl = id ? "update_question.php" : "insert_question.php";

  try {
    const res = await fetch(apiUrl, {
      method: "POST",
      body: formData
    });
    const data = await res.json();
    alert(data.message);
    if (!id) document.getElementById("questionForm").reset();
    resetPreview();
    refreshIframe();
    formChanged = false;
  } catch (err) {
    alert("❌ " + err.message);
  } finally {
    saveBtn.disabled = false;
  }
}

// ========== 4. Delete ==========
function deleteQuestion() {
  const id = document.getElementById("question_id").value;
  if (!id || !confirm("Bạn có chắc muốn xoá?")) return;

  fetch("delete_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + encodeURIComponent(id)
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      if (data.status === "success") {
        document.getElementById("questionForm").reset();
        resetPreview();
        refreshIframe();
      }
    });
}

// ========== 5. Image Preview ==========
document.getElementById("image").addEventListener("change", function () {
  const file = this.files[0];
  const preview = document.getElementById("imagePreview");
  const deleteCheckbox = document.getElementById("delete_image");
  const deleteLabel = document.getElementById("deleteImageLabel");

  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.classList.add("show");
      deleteCheckbox.checked = false;
      deleteLabel.style.display = "inline-block";
      debounceFullPreview();
    };
    reader.readAsDataURL(file);
  } else {
    preview.src = "";
    preview.classList.remove("show");
    deleteCheckbox.checked = false;
    deleteLabel.style.display = "none";
    debounceFullPreview();
  }
});

// ========== 6. Đồng bộ từ iframe ==========
window.addEventListener("message", function (event) {
  if (event.data?.type === "fillForm") {
    const data = event.data.data;
    document.getElementById("question_id").value = data.id;
    document.getElementById("topic").value = data.topic;
    document.getElementById("question").value = data.question;
    document.getElementById("answer1").value = data.answer1;
    document.getElementById("answer2").value = data.answer2;
    document.getElementById("answer3").value = data.answer3;
    document.getElementById("answer4").value = data.answer4;
    document.getElementById("correct_answer").value = data.correct_answer;

    const previewImg = document.getElementById("imagePreview");
    if (data.image) {
      previewImg.src = data.image;
      previewImg.classList.add("show");
      document.getElementById("image_url").value = data.image;
      document.getElementById("deleteImageLabel").style.display = "inline-block";
    } else {
      previewImg.src = "";
      previewImg.classList.remove("show");
      document.getElementById("image_url").value = "";
      document.getElementById("deleteImageLabel").style.display = "none";
    }

    ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
    debounceFullPreview();
  }
});

// ========== 7. Xem trước toggle ==========
document.addEventListener("DOMContentLoaded", () => {
  togglePreview();
  document.getElementById("togglePreview").addEventListener("change", togglePreview);
});

// ========== 8. Cảnh báo khi rời trang ==========
let formChanged = false;
document.getElementById("questionForm").addEventListener("input", () => formChanged = true);
window.addEventListener("beforeunload", function (e) {
  if (formChanged) {
    e.preventDefault();
    e.returnValue = "";
  }
});

// ========== 9. Export các hàm (nếu cần gọi ngoài) ==========
export {
  saveQuestion,
  deleteQuestion,
  renderPreview,
  resetPreview
};
