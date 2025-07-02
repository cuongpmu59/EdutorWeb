<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

  <style>
  /* ===== Reset & Base ===== */
  * {
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  body {
    margin: 20px;
    background: #f8f9fa;
    color: #333;
    line-height: 1.6;
  }

  h2, h3 {
    color: #2c3e50;
    margin-bottom: 15px;
  }

  label {
    display: block;
    margin-top: 12px;
    font-weight: 500;
  }

  /* ===== Form Layout ===== */
  .question-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #ddd;
    margin-bottom: 20px;
  }

  .two-column .form-left {
    flex: 2;
  }

  .two-column .form-right {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* ===== Input & Textarea ===== */
  input[type="text"],
  input[type="file"],
  select,
  textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-top: 5px;
    font-size: 14px;
  }

  textarea {
    resize: vertical;
  }

  /* ===== Buttons ===== */
  .button-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  button {
    padding: 10px 15px;
    font-size: 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.2s;
    background-color: #3498db;
    color: white;
  }

  button:hover {
    background-color: #2980b9;
  }

  .delete-btn {
    background-color: #e74c3c;
  }

  .delete-btn:hover {
    background-color: #c0392b;
  }

  .reset-btn {
    background-color: #95a5a6;
  }

  .reset-btn:hover {
    background-color: #7f8c8d;
  }

  /* ===== Preview Styles ===== */
  .latex-preview {
    margin-top: 5px;
    background: #f0f4f8;
    padding: 8px 12px;
    border-left: 4px solid #3498db;
    border-radius: 4px;
    font-style: italic;
    min-height: 24px;
    color: #2c3e50;
  }

  #imagePreview {
    display: none;
    max-width: 100%;
    margin-top: 10px;
    border-radius: 8px;
    border: 1px solid #ddd;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }

  /* ===== Full Preview Box ===== */
  .full-preview {
    background: #fcfcfc;
    padding: 15px;
    border: 1px dashed #aaa;
    border-radius: 8px;
    margin-top: 10px;
  }

  .full-preview p {
    margin: 5px 0;
  }

  /* ===== Dark Mode Support ===== */
  @media (prefers-color-scheme: dark) {
    body {
      background: #1e1e1e;
      color: #eee;
    }

    .question-container {
      background: #2c2c2c;
      border-color: #444;
    }

    input, textarea, select {
      background: #333;
      color: #fff;
      border: 1px solid #555;
    }

    .latex-preview,
    .full-preview {
      background: #2a2a2a;
      color: #fff;
    }

    .latex-preview {
      border-left-color: #6ab0f3;
    }

    #imagePreview {
      border-color: #555;
    }

    button {
      background-color: #3498db;
    }

    button:hover {
      background-color: #2980b9;
    }

    .reset-btn {
      background-color: #7f8c8d;
    }

    .reset-btn:hover {
      background-color: #95a5a6;
    }

    .delete-btn {
      background-color: #e74c3c;
    }

    .delete-btn:hover {
      background-color: #c0392b;
    }
  }
  </style>
</head>

<body>
  <h2>Nhập câu hỏi</h2>
  <label><input type="checkbox" id="togglePreview" checked onchange="togglePreview()"> Hiện xem trước công thức</label>

  <form id="questionForm" enctype="multipart/form-data">
    <div class="question-container two-column">
      <div class="form-left">
        <input type="hidden" name="id" id="question_id">

        <label for="topic">Chủ đề:</label>
        <input type="text" name="topic" id="topic" required>

        <label for="question">Câu hỏi:</label>
        <textarea name="question" id="question" rows="3" required oninput="renderPreview('question')"></textarea>
        <div id="preview_question" class="latex-preview"></div>

        <div id="answersContainer"></div>

        <label for="correct_answer">Đáp án đúng:</label>
        <select name="correct_answer" id="correct_answer" required>
          <option value="">--Chọn--</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>

        <label for="image">Ảnh minh hoạ:</label>
        <input type="file" name="image" id="image">
        <small id="imageFileName" class="text-muted"></small>
        <img id="imagePreview">
        <input type="hidden" name="image_url" id="image_url">

        <label id="deleteImageLabel" style="display:none;">
          <input type="checkbox" id="delete_image"> Xóa ảnh minh hoạ
        </label>
      </div>

      <div class="form-right">
      <div class="button-group">
        <button type="button" onclick="handleSaveQuestion(false)">➕ Thêm</button>
        <button type="button" onclick="handleSaveQuestion(true)">✏️ Sửa</button>
        <button type="button" class="delete-btn" onclick="deleteQuestion()">🗑️ Xoá</button>
        <button type="reset" class="reset-btn" onclick="resetForm()">🔄 Làm mới</button>
        <button type="button" onclick="exportToPDF()">📄 Xuất đề thi PDF</button>
      </div>

      </div>
    </div>

    <label style="margin-top:15px; display:inline-block;">
      <input type="checkbox" id="showPreview"
        onchange="togglePreviewBox('showPreview', 'fullPreviewBox'); debounceFullPreview()">
      Xem trước toàn bộ
    </label>
    <div id="fullPreviewBox" class="full-preview" style="display: none;">
      <h5>Xem trước toàn bộ:</h5>
      <div id="fullPreview" class="full-preview"></div>
    </div>
  </form>

  <h3>Danh sách câu hỏi</h3>

  <iframe id="questionIframe" src="get_question.php" width="100%" height="500" style="border:1px solid #ccc; border-radius: 8px;"></iframe>

  <script src="js/question_script.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const container = document.getElementById("answersContainer");
      ["A", "B", "C", "D"].forEach((label, i) => {
        const id = `answer${i + 1}`;
        container.insertAdjacentHTML("beforeend", `
          <label for="${id}">Đáp án ${label}:</label>
          <input type="text" name="${id}" id="${id}" required oninput="renderPreview('${id}')">
          <div id="preview_${id}" class="latex-preview"></div>
        `);
      });
    });

    window.addEventListener("message", function (event) {
      if (event.origin !== window.location.origin) return;
      if (event.data.type === "fillForm" && event.data.data) {
        const d = event.data.data;
        ["id", "topic", "question", "answer1", "answer2", "answer3", "answer4", "correct_answer", "image_url"].forEach(id => {
          const el = document.getElementById(id === "id" ? "question_id" : id);
          if (el) el.value = d[id] || "";
        });

        const hasImg = d.image;
        const imgPreview = document.getElementById("imagePreview");
        imgPreview.style.display = hasImg ? "block" : "none";
        imgPreview.src = hasImg || "";

        document.getElementById("deleteImageLabel").style.display = hasImg ? "inline-block" : "none";

        ["question", "answer1", "answer2", "answer3", "answer4"].forEach(renderPreview);
        formChanged = false;
      }
    });

    function debounceFullPreview() {
      clearTimeout(window._previewTimer);
      window._previewTimer = setTimeout(() => {
        const q = document.getElementById("question").value;
        const a = ["answer1", "answer2", "answer3", "answer4"].map(id => document.getElementById(id).value);
        const correct = document.getElementById("correct_answer").value;

        const content = `
          <p><strong>Chủ đề:</strong> ${document.getElementById("topic").value}</p>
          <p><strong>Câu hỏi:</strong> ${q}</p>
          ${a.map((text, i) => `<p><strong>Đáp án ${String.fromCharCode(65 + i)}:</strong> ${text}</p>`).join("")}
          <p><strong>Đáp án đúng:</strong> ${correct}</p>
        `;
        document.getElementById("fullPreview").innerHTML = content;

        if (window.MathJax?.typesetPromise) {
          MathJax.typesetPromise([document.getElementById("fullPreview")]);
        }
      }, 300);
    }

    
  function exportToPDF() {
  const form = document.createElement("form");
  form.method = "POST";
  form.action = "export_exam_pdf.php";
  form.target = "_blank";

  const fields = [
    "question_id", "topic", "question", "answer1", "answer2", "answer3", "answer4", "correct_answer", "image_url"
  ];

  fields.forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      const hidden = document.createElement("input");
      hidden.type = "hidden";
      hidden.name = id;
      hidden.value = input.value || "";
      form.appendChild(hidden);
    }
  });

  document.body.appendChild(form);
  form.submit();
  form.remove();
}
function validateForm() {
  const requiredFields = ["topic", "question", "answer1", "answer2", "answer3", "answer4", "correct_answer"];
  for (const id of requiredFields) {
    const el = document.getElementById(id);
    if (!el.value.trim()) {
      alert(`Vui lòng nhập đầy đủ thông tin: ${id}`);
      el.focus();
      return false;
    }
  }
  return true;
}
function deleteQuestion() {
  const id = document.getElementById("question_id").value;
  if (!id) return alert("Vui lòng chọn câu hỏi để xoá.");
  if (!confirm("Bạn có chắc muốn xoá câu hỏi này?")) return;

  fetch("delete_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert("Đã xoá câu hỏi.");
      resetForm();
      refreshIframe();
    } else {
      alert("Lỗi khi xoá câu hỏi.");
    }
  });
}
function togglePreviewBox(checkboxId, previewId) {
  const box = document.getElementById(previewId);
  const checked = document.getElementById(checkboxId).checked;
  box.style.display = checked ? "block" : "none";
  if (checked) debounceFullPreview();
}
function togglePreview() {
  const show = document.getElementById("togglePreview").checked;
  document.querySelectorAll(".latex-preview").forEach(div => {
    div.style.display = show ? "block" : "none";
  });
}
function refreshIframe() {
  const iframe = document.getElementById("questionIframe");
  if (iframe && iframe.contentWindow) {
    iframe.contentWindow.location.reload();
  }
}

function resetForm() {
  document.getElementById("questionForm").reset();
  document.getElementById("question_id").value = "";
  document.getElementById("imagePreview").style.display = "none";
  document.getElementById("imagePreview").src = "";
  document.getElementById("deleteImageLabel").style.display = "none";
  togglePreview();  // cập nhật lại trạng thái preview
  debounceFullPreview(); // cập nhật lại preview toàn bộ nếu cần
}
// Hàm chính để xử lý lưu thêm/sửa
async function handleSaveQuestion(isEdit) {
  const id = $("question_id").value.trim();
  const formData = getFormData();
  formData.set("delete_image", $("delete_image").checked ? "1" : "0");

  for (let field of ["question", "answer1", "answer2", "answer3", "answer4", "correct_answer", "topic"]) {
    if (!formData.get(field)?.trim()) return alert("Vui lòng điền đầy đủ thông tin.");
  }

  const file = formData.get("image");
  if (file?.size > 0) {
    if (!file.type.startsWith("image/")) return alert("Chỉ chấp nhận ảnh.");
    if (file.size > 2 * 1024 * 1024) return alert("Ảnh quá lớn. < 2MB thôi.");
  }

  const buttons = document.querySelectorAll(".form-right button");
  buttons.forEach(btn => btn.disabled = true);

  try {
    // Upload ảnh nếu có
    if (file?.size > 0) {
      const upForm = new FormData();
      upForm.append("file", file);
      upForm.append("upload_preset", "quiz_photo");

      const res = await fetch("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/upload", {
        method: "POST",
        body: upForm
      });

      const data = await res.json();
      if (!data.secure_url) {
        throw new Error("Lỗi upload ảnh: " + (data.error?.message || "Không rõ nguyên nhân."));
      }else{
        formData.set("image_url", data.secure_url);
      }
    }

    const api = isEdit ? "update_question.php" : "insert_question.php";
    const res = await fetch(api, {
      method: "POST",
      body: formData
    });

    const result = await res.json();
    if (!res.ok) throw new Error(result.message || "Lỗi không xác định");

    alert(result.message);

    // Đặt lại form nếu thêm mới, nếu sửa thì chỉ làm mới preview
    if (!isEdit) {
      resetForm();
    } else {
      resetPreview();
    }

    refreshIframe();
    $("questionIframe").scrollIntoView({ behavior: "smooth" });
    formChanged = false;

  } catch (e) {
    alert("❌ " + (e.message || "Lỗi khi lưu câu hỏi."));
  } finally {
    buttons.forEach(btn => btn.disabled = false);
  }
}


  </script>
</body>
</html>
