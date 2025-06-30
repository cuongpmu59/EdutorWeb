<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
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
        </div>
      </div>
    </div>

    <label style="margin-top:15px; display:inline-block;">
      <input type="checkbox" id="showPreview"
        onchange="togglePreviewBox('showPreview', 'fullPreviewBox'); debounceFullPreview()">
      Xem trước toàn bộ
    </label>
    <div id="fullPreviewBox" class="full-preview; style="display: none; margin-top: 15px; padding: 10px; border: 1px dashed #ccc;"></div>
  </form>

  <h3 style="display: flex; justify-content: space-between; align-items: center;">
    <span>Danh sách câu hỏi</span>
    <span style="display: flex; gap: 10px; align-items: center;">
      <button onclick="document.getElementById('importFile').click()">📅 Nhập Excel</button>
      <input type="file" id="importFile" style="display:none" accept=".xlsx,.xls" onchange="importExcel(this.files[0])">
      <button onclick="exportToExcel()">📄 Xuất Excel</button>
      </span>
  </h3>

  <iframe id="questionIframe" src="get_question.php" width="100%" height="500" style="border:1px solid #ccc;"></iframe>

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
  </script>
</body>
</html>
