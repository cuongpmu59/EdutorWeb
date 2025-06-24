// question_script.js - hoàn chỉnh, hỗ trợ topic, Cloudinary, kiểm tra trùng lặp, MathJax

function getFormData() {
  const form = document.getElementById("questionForm");
  return new FormData(form);
}

function saveQuestion() {
  const id = document.getElementById("question_id").value.trim();
  const form = document.getElementById("questionForm");
  const formData = new FormData(form);
  const deleteImage = document.getElementById("delete_image").checked;
  formData.set("delete_image", deleteImage ? "1" : "0");

  const requiredFields = ["topic", "question", "answer1", "answer2", "answer3", "answer4", "correct_answer"];
  for (const field of requiredFields) {
    if (!formData.get(field)?.trim()) {
      alert("Vui lòng nhập đầy đủ thông tin câu hỏi, chủ đề và đáp án.");
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
      alert("Ảnh quá lớn. Vui lòng chọn ảnh dưới 2MB.");
      return;
    }
  }

  if (!id) {
    fetch("check_duplicate.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "question=" + encodeURIComponent(formData.get("question"))
    })
    .then(res => res.json())
    .then(data => {
      if (data.exists) {
        alert("❌ Câu hỏi đã tồn tại.");
      } else {
        submitQuestion(formData, form, id);
      }
    })
    .catch(err => {
      console.error("Lỗi kiểm tra trùng lặp:", err);
      alert("Không thể kiểm tra trùng lặp.");
    });
  } else {
    if (confirm("Bạn có chắc muốn cập nhật câu hỏi này?")) {
      submitQuestion(formData, form, id);
    }
  }
}

async function submitQuestion(formData, form, id) {
  const url = id ? "update_question.php" : "insert_question.php";
  const imageFile = formData.get("image");

  if (imageFile && imageFile.size > 0 && formData.get("delete_image") !== "1") {
    const cloudForm = new FormData();
    cloudForm.append("file", imageFile);
    cloudForm.append("upload_preset", "quiz_photo");

    try {
      const res = await fetch("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/upload", {
        method: "POST",
        body: cloudForm
      });
      const data = await res.json();
      if (data.secure_url) {
        formData.set("image_url", data.secure_url);
        document.getElementById("downloadImage").href = data.secure_url;
        document.getElementById("downloadImage").style.display = "inline";
      }
    } catch (err) {
      console.error("Upload Cloudinary lỗi:", err);
      alert("Không thể tải ảnh lên Cloudinary.");
      return;
    }
  } else {
    formData.set("image_url", "");
    document.getElementById("downloadImage").style.display = "none";
  }

  fetch(url, {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(response => {
    alert(response);
    refreshIframe();
    if (!id) form.reset();
    resetPreview();
    formChanged = false;
  })
  .catch(err => {
    console.error("Lỗi lưu:", err);
    alert("Không thể lưu câu hỏi.");
  });
}

function deleteQuestion() {
  const id = document.getElementById("question_id").value.trim();
  if (!id) {
    alert("Vui lòng chọn câu hỏi cần xoá.");
    return;
  }

  if (!confirm("Bạn có chắc muốn xoá câu hỏi này?")) return;

  fetch("delete_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + encodeURIComponent(id)
  })
  .then(res => res.text())
  .then(response => {
    alert(response);
    document.getElementById("questionForm").reset();
    resetPreview();
    refreshIframe();
  })
  .catch(err => {
    console.error("Lỗi xóa:", err);
    alert("Không thể xoá câu hỏi.");
  });
}

function resetPreview() {
  const img = document.getElementById("imagePreview");
  img.src = "";
  img.style.display = "none";
  const deleteLabel = document.getElementById("deleteImageLabel");
  if (deleteLabel) deleteLabel.style.display = "none";
  document.getElementById("delete_image").checked = false;
  document.getElementById("downloadImage").style.display = "none";
}

function togglePreview() {
  const isChecked = document.getElementById("togglePreview").checked;
  const previews = document.querySelectorAll(".latex-preview");
  previews.forEach(div => div.style.display = isChecked ? "block" : "none");

  const full = document.getElementById("fullPreview");
  if (full) full.style.display = isChecked ? "block" : "none";
}

function updateFullPreview() {
  const topic = document.getElementById("topic").value;
  const q = document.getElementById("question").value;
  const a = document.getElementById("answer1").value;
  const b = document.getElementById("answer2").value;
  const c = document.getElementById("answer3").value;
  const d = document.getElementById("answer4").value;
  const correct = document.getElementById("correct_answer").value;

  const html = `
    <p><strong>Chủ đề:</strong> ${topic}</p>
    <p><strong>Câu hỏi:</strong> \\(${q}\\)</p>
    <ul>
      <li><strong>A.</strong> ${a}</li>
      <li><strong>B.</strong> ${b}</li>
      <li><strong>C.</strong> ${c}</li>
      <li><strong>D.</strong> ${d}</li>
    </ul>
    <p><strong>Đáp án đúng:</strong> ${correct}</p>
  `;

  const preview = document.getElementById("fullPreview");
  preview.innerHTML = html;
  if (window.MathJax) MathJax.typesetPromise([preview]);
}

function renderPreview(fieldId) {
  const val = document.getElementById(fieldId).value;
  const div = document.getElementById("preview_" + fieldId);
  div.innerHTML = `\\(${val}\\)`;
  if (window.MathJax) MathJax.typesetPromise([div]);
  updateFullPreview();
}

function searchQuestion() {
  const keyword = prompt("Nhập từ khóa cần tìm:");
  if (!keyword) return;

  fetch("search_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "keyword=" + encodeURIComponent(keyword)
  })
  .then(res => res.json())
  .then(data => {
    if (data.length === 0) {
      alert("Không tìm thấy câu hỏi nào.");
    } else {
      alert("Tìm thấy " + data.length + " câu hỏi.");
      console.log(data);
    }
  })
  .catch(err => {
    console.error("Tìm kiếm lỗi:", err);
    alert("Không thể tìm kiếm.");
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

document.getElementById("image").addEventListener("change", function () {
  const file = this.files[0];
  const preview = document.getElementById("imagePreview");
  const deleteBox = document.getElementById("delete_image");
  const deleteLabel = document.getElementById("deleteImageLabel");

  if (file) {
    if (!file.type.startsWith("image/") || file.size > 2 * 1024 * 1024) {
      alert("Ảnh không hợp lệ.");
      this.value = "";
      return;
    }
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = "block";
      deleteBox.checked = false;
      deleteLabel.style.display = "inline-block";
    };
    reader.readAsDataURL(file);
  } else {
    resetPreview();
  }
});

let formChanged = false;
document.getElementById("questionForm").addEventListener("input", () => {
  formChanged = true;
});

window.addEventListener("beforeunload", (e) => {
  if (formChanged) {
    e.preventDefault();
    e.returnValue = "";
  }
});
