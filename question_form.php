<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
  <h2>Nhập câu hỏi</h2>
  <label><input type="checkbox" id="togglePreview" checked onchange="togglePreview()"> Hiện xem trước công thức</label><br>

  <form id="questionForm" enctype="multipart/form-data">
    <div class="question-container">
      <div class="form-left">
        <input type="hidden" name="id" id="question_id">

        <label for="question">Câu hỏi:</label>
        <textarea name="question" id="question" rows="3" required oninput="renderPreview('question')"></textarea>
        <div id="preview_question" class="latex-preview"></div>

        <label for="answer1">Đáp án A:</label>
        <input type="text" name="answer1" id="answer1" required oninput="renderPreview('answer1')">
        <div id="preview_answer1" class="latex-preview"></div>

        <label for="answer2">Đáp án B:</label>
        <input type="text" name="answer2" id="answer2" required oninput="renderPreview('answer2')">
        <div id="preview_answer2" class="latex-preview"></div>

        <label for="answer3">Đáp án C:</label>
        <input type="text" name="answer3" id="answer3" required oninput="renderPreview('answer3')">
        <div id="preview_answer3" class="latex-preview"></div>

        <label for="answer4">Đáp án D:</label>
        <input type="text" name="answer4" id="answer4" required oninput="renderPreview('answer4')">
        <div id="preview_answer4" class="latex-preview"></div>

        <label for="correct_answer">Đáp án đúng:</label>
        <select name="correct_answer" id="correct_answer" required>
          <option value="">--Chọn--</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>

        <label for="image">Ảnh minh họa:</label>
        <input type="file" name="image" id="image">
        <img id="imagePreview">
        <label id="deleteImageLabel" style="display:none;">
          <input type="checkbox" id="delete_image"> Xóa ảnh minh họa
        </label>
      </div>

      <div class="form-right">
        <button type="button" onclick="saveQuestion()">Lưu</button>
        <button type="button" class="delete-btn" onclick="deleteQuestion()">Xoá</button>
        <button type="button" class="search-btn" onclick="searchQuestion()">Tìm kiếm</button>
        <button type="reset" class="reset-btn" onclick="resetPreview()">Làm mới</button>
        <button type="button" onclick="document.getElementById('importFile').click()">📥 Nhập file</button>
        <input type="file" id="importFile" style="display: none;" accept=".csv" onchange="importFile(this.files[0])">
        <button type="button" onclick="exportToCSV()">📤 Xuất file</button>
      </div>
    </div>

    <h3>Xem trước toàn bộ câu hỏi</h3>
    <div id="fullPreview" class="full-preview"></div>
  </form>

  <div id="message"></div>

  <h3>Danh sách câu hỏi</h3>
  <iframe id="questionIframe" src="get_question.php" width="100%" height="400"></iframe>

  <script src="js/question_script.js"></script>
  <script>
    document.getElementById("image").addEventListener("change", function (event) {
      const file = event.target.files[0];
      const preview = document.getElementById("imagePreview");

      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = "block";
        };
        reader.readAsDataURL(file);
      } else {
        preview.src = "";
        preview.style.display = "none";
      }
    });

    function togglePreview() {
      const show = document.getElementById("togglePreview").checked;
      document.querySelectorAll(".latex-preview").forEach(el => {
        el.style.display = show ? "block" : "none";
      });
    }

    window.addEventListener("message", function (event) {
      if (event.data.type === "fillForm") {
        window.scrollTo({ top: 0, behavior: 'smooth' });

        const data = event.data.data;
        document.getElementById("question_id").value = data.id;
        document.getElementById("question").value = data.question;
        document.getElementById("answer1").value = data.answer1;
        document.getElementById("answer2").value = data.answer2;
        document.getElementById("answer3").value = data.answer3;
        document.getElementById("answer4").value = data.answer4;
        document.getElementById("correct_answer").value = data.correct_answer;

        const imgPreview = document.getElementById("imagePreview");
        if (data.image) {
          imgPreview.src = data.image;
          imgPreview.style.display = "block";
        } else {
          imgPreview.src = "";
          imgPreview.style.display = "none";
        }

        ['question', 'answer1', 'answer2', 'answer3', 'answer4'].forEach(id => renderPreview(id));
        updateFullPreview();
      }
    });
  </script>
</body>
</html>