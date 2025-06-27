import {
  renderPreview,
  resetPreview,
  debounceFullPreview,
  togglePreview
} from './preview_module.js';

function addQuestion() {
  document.getElementById("questionForm").reset();
  document.getElementById("question_id").value = "";
  const img = document.getElementById("previewImage");
  if (img) {
    img.src = "";
    img.style.display = "none";
  }
  const deleteLabel = document.getElementById("deleteImageLabel");
  if (deleteLabel) deleteLabel.style.display = "none";

  formChanged = false;
  resetPreview();
}

function updateQuestion() {
  // Tùy bạn triển khai — bạn có thể gọi saveQuestion() nếu phù hợp
  saveQuestion();
}

async function saveQuestion() {
  const id = document.getElementById("question_id").value.trim();
  const formData = new FormData(document.getElementById("questionForm"));

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
  }
}

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

function refreshIframe() {
  const iframe = document.getElementById("questionIframe");
  if (iframe) {
    iframe.contentWindow.location.reload();
    iframe.onload = () => {
      if (iframe.contentWindow.MathJax) {
        iframe.contentWindow.MathJax.typesetPromise();
      }
    };
  }
}

function previewFull() {
  debounceFullPreview();
}

function openSearchModal() {
  document.getElementById("searchModal").style.display = "block";
}

function closeSearchModal() {
  document.getElementById("searchModal").style.display = "none";
}

function searchQuestion() {
  const keyword = document.getElementById("searchKeyword").value.toLowerCase();
  const rows = document.querySelectorAll("#searchResultTable tbody tr");
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(keyword) ? "" : "none";
  });
}

// Đồng bộ với iframe
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

    const img = document.getElementById("previewImage");
    if (data.image) {
      img.src = data.image;
      img.style.display = "block";
    } else {
      img.src = "";
      img.style.display = "none";
    }

    ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
    debounceFullPreview();
  }
});

// Auto-toggle preview
document.addEventListener("DOMContentLoaded", () => {
  togglePreview();
  document.getElementById("togglePreview").addEventListener("change", togglePreview);
});

// Cảnh báo rời trang
let formChanged = false;
document.getElementById("questionForm").addEventListener("input", () => formChanged = true);
window.addEventListener("beforeunload", function (e) {
  if (formChanged) {
    e.preventDefault();
    e.returnValue = "";
  }
});

export {
  addQuestion,
  updateQuestion,
  deleteQuestion,
  previewFull,
  openSearchModal,
  closeSearchModal,
  searchQuestion
};
