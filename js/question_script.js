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

function renderMathInPage() {
  if (!window.MathJax) return;
  if (containsMath(document.body.innerText)) {
    MathJax.typesetPromise();
  }
}

// ========== 2. Preview Handling ==========
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
  const q = document.getElementById("question").value;
  const a = document.getElementById("answer1").value;
  const b = document.getElementById("answer2").value;
  const c = document.getElementById("answer3").value;
  const d = document.getElementById("answer4").value;
  const correct = document.getElementById("correct_answer").value;

  const html = `
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
  debounceRenderMath(preview);
}

function togglePreview() {
  const isChecked = document.getElementById("togglePreview").checked;
  document.querySelectorAll(".latex-preview").forEach(div => {
    div.style.display = isChecked ? "block" : "none";
  });
}

function toggleFullPreview() {
  const isChecked = document.getElementById("toggleFullPreview").checked;
  const preview = document.getElementById("fullPreview");
  preview.style.display = isChecked ? "block" : "none";
}

function resetPreview() {
  document.getElementById("imagePreview").classList.remove("show");
  document.getElementById("image_url").value = "";
  document.getElementById("delete_image").checked = false;
  document.getElementById("deleteImageLabel").style.display = "none";
  debounceFullPreview();
}

// ========== 3. Form Submission ==========
function saveQuestion() {
  const id = document.getElementById("question_id").value.trim();
  const formData = getFormData();
  formData.set("delete_image", document.getElementById("delete_image").checked ? "1" : "0");

  const requiredFields = ["question", "answer1", "answer2", "answer3", "answer4", "correct_answer"];
  for (let field of requiredFields) {
    if (!formData.get(field)?.trim()) {
      alert("Vui lòng điền đủ thông tin câu hỏi và đáp án.");
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

  const saveBtn = document.querySelector(".form-right button:nth-child(1)");
  saveBtn.disabled = true;

  if (!id) {
    fetch("check_duplicate.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "question=" + encodeURIComponent(formData.get("question"))
    })
    .then(res => res.json())
    .then(data => {
      if (data.exists) {
        alert("Câu hỏi này đã tồn tại.");
        saveBtn.disabled = false;
      } else {
        submitQuestion(formData, id, saveBtn);
      }
    })
    .catch(err => {
      console.error(err);
      alert("Lỗi kiểm tra trùng lặp: " + err.message);
      saveBtn.disabled = false;
    });
  } else {
    if (confirm("Bạn có chắc muốn cập nhật?")) {
      submitQuestion(formData, id, saveBtn);
    } else {
      saveBtn.disabled = false;
    }
  }
}

async function submitQuestion(formData, id, saveBtn) {
  const url = id ? "update_question.php" : "insert_question.php";
  const imageFile = formData.get("image");

  if (formData.get("delete_image") === "1") {
    formData.set("image_url", "");
  } else if (imageFile && imageFile.size > 0) {
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
      }
    } catch (err) {
      alert("Không thể tải ảnh lên Cloudinary: " + err.message);
      saveBtn.disabled = false;
      return;
    }
  }

  fetch(url, {
    method: "POST",
    body: formData
  })
    .then(res => res.text())
    .then(response => {
      alert(response);
      if (!id) document.getElementById("questionForm").reset();
      resetPreview();
      refreshIframe();
      formChanged = false;
    })
    .catch(err => {
      console.error(err);
      alert("Lỗi khi lưu câu hỏi: " + err.message);
    })
    .finally(() => {
      saveBtn.disabled = false;
    });
}

function deleteQuestion() {
  const id = document.getElementById("question_id").value.trim();
  if (!id) return alert("Chọn câu hỏi cần xoá.");
  if (!confirm("Chắc chắn xoá?")) return;

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
      console.error(err);
      alert("Xoá thất bại: " + err.message);
    });
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
      showSearchModal(data);
    }
  })
  .catch(err => {
    console.error("Lỗi:", err);
    alert("Tìm kiếm thất bại: " + err.message);
  });
}

function showSearchModal(data) {
  const modal = document.getElementById("searchModal");
  const tbody = document.querySelector("#searchResultsTable tbody");
  tbody.innerHTML = "";

  data.forEach(item => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${item.id}</td>
      <td>${item.topic}</td>
      <td>${item.question}</td>
      <td>${item.correct_answer}</td>
    `;
    row.addEventListener("click", () => {
      window.postMessage({ type: "fillForm", data: item }, "*");
      row.style.backgroundColor = "#e0f7fa";
      closeSearchModal();
    });
    tbody.appendChild(row);
  });

  modal.style.display = "flex";
}

function closeSearchModal() {
  document.getElementById("searchModal").style.display = "none";
}

// ========== 4. Ảnh minh họa ==========
document.getElementById("image").addEventListener("change", function () {
  const file = this.files[0];
  const preview = document.getElementById("imagePreview");
  const deleteCheckbox = document.getElementById("delete_image");
  const deleteLabel = document.getElementById("deleteImageLabel");

  if (file) {
    if (!file.type.startsWith("image/")) {
      alert("Chỉ chấp nhận file ảnh!");
      this.value = "";
      return;
    }
    if (file.size > 2 * 1024 * 1024) {
      alert("Ảnh quá lớn. Vui lòng chọn ảnh dưới 2MB.");
      this.value = "";
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.classList.add("show");
      deleteCheckbox.checked = false;
      deleteLabel.style.display = "inline-block";
    };
    reader.readAsDataURL(file);
  } else {
    preview.src = "";
    preview.classList.remove("show");
    deleteCheckbox.checked = false;
    deleteLabel.style.display = "none";
  }
});

// ========== 5. Tự động đồng bộ ==========
window.addEventListener("message", function (event) {
  if (event.data.type === "fillForm") {
    const data = event.data.data;
    document.getElementById("question_id").value = data.id;
    document.getElementById("topic").value = data.topic;
    document.getElementById("question").value = data.question;
    document.getElementById("answer1").value = data.answer1;
    document.getElementById("answer2").value = data.answer2;
    document.getElementById("answer3").value = data.answer3;
    document.getElementById("answer4").value = data.answer4;
    document.getElementById("correct_answer").value = data.correct_answer;

    if (data.image) {
      const imgPreview = document.getElementById("imagePreview");
      imgPreview.src = data.image;
      imgPreview.classList.add("show");
      document.getElementById("image_url").value = data.image;
      document.getElementById("deleteImageLabel").style.display = "inline-block";
    } else {
      document.getElementById("imagePreview").src = "";
      document.getElementById("imagePreview").classList.remove("show");
      document.getElementById("image_url").value = "";
      document.getElementById("deleteImageLabel").style.display = "none";
    }

    ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
    debounceFullPreview();
  }
});

// ========== 6. Cảnh báo nếu thoát ==========
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

// ========== 7. Khởi tạo ==========
document.addEventListener("DOMContentLoaded", () => {
  togglePreview();
  toggleFullPreview();
});
