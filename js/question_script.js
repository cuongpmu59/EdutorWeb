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

function containsMath(content) {
  return /\\\(|\\\[|\$\$/.test(content);
}

let mathJaxTimer;
function debounceRenderMath(element) {
  clearTimeout(mathJaxTimer);
  mathJaxTimer = setTimeout(() => {
    if (window.MathJax && containsMath(element.innerText)) {
      MathJax.typesetPromise([element]);
    }
  }, 250);
}

// ========== 2. Preview ==========

function renderPreview(fieldId) {
  const value = document.getElementById(fieldId).value;
  const previewDiv = document.getElementById("preview_" + fieldId);
  previewDiv.innerHTML = value;
  debounceRenderMath(previewDiv);
  debounceFullPreview();
}

let previewTimer;
function debounceFullPreview() {
  clearTimeout(previewTimer);
  previewTimer = setTimeout(updateFullPreview, 300);
}

function updateFullPreview() {
  document.getElementById("pv_id").textContent = document.getElementById("question_id").value;
  document.getElementById("pv_topic").textContent = document.getElementById("topic").value;
  document.getElementById("pv_question").innerHTML = document.getElementById("question").value;
  document.getElementById("pv_a").innerHTML = document.getElementById("answer1").value;
  document.getElementById("pv_b").innerHTML = document.getElementById("answer2").value;
  document.getElementById("pv_c").innerHTML = document.getElementById("answer3").value;
  document.getElementById("pv_d").innerHTML = document.getElementById("answer4").value;
  document.getElementById("pv_correct").textContent = document.getElementById("correct_answer").value;

  const img = document.getElementById("imagePreview");
  const pvImg = document.getElementById("pv_image");
  if (img && img.src && img.style.display !== "none") {
    pvImg.src = img.src;
    pvImg.style.display = "block";
  } else {
    pvImg.src = "";
    pvImg.style.display = "none";
  }

  if (window.MathJax) {
    MathJax.typesetPromise([document.getElementById("previewBox")]);
  }
}

function togglePreview() {
  const isChecked = document.getElementById("togglePreview").checked;
  document.getElementById("previewBox").style.display = isChecked ? "block" : "none";
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
