<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="css/styles.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
  <h2>Nhập câu hỏi</h2>
  <form id="questionForm" enctype="multipart/form-data">
    <input type="hidden" name="id" id="question_id">

    <label>Câu hỏi:</label>
    <textarea name="question" id="question" rows="3" required></textarea><br>

    <label>Đáp án A:</label>
    <input type="text" name="answer1" id="answer1" required><br>

    <label>Đáp án B:</label>
    <input type="text" name="answer2" id="answer2" required><br>

    <label>Đáp án C:</label>
    <input type="text" name="answer3" id="answer3" required><br>

    <label>Đáp án D:</label>
    <input type="text" name="answer4" id="answer4" required><br>

    <label>Đáp án đúng:</label>
    <select name="correct_answer" id="correct_answer" required>
      <option value="">--Chọn--</option>
      <option value="A">A</option>
      <option value="B">B</option>
      <option value="C">C</option>
      <option value="D">D</option>
    </select><br>

    <label>Ảnh minh họa:</label>
    <input type="file" name="image" id="image"><br>
    <img id="imagePreview" style="display:none; max-height:100px; margin:10px 0;" />

    <div class="button-group">
      <button type="button" onclick="saveQuestion()">Lưu</button>
      <button type="button" onclick="deleteQuestion()">Xoá</button>
      <button type="button" onclick="searchQuestion()">Tìm kiếm</button>
      <button type="reset">Làm mới</button>
      <button type="button" onclick="document.getElementById('importFile').click()">📥 Nhập file</button>
        <input type="file" id="importFile" style="display: none;" accept=".csv" onchange="importFile(this.files[0])">
        <button type="button" onclick="exportToCSV()">📤 Xuất file</button>
    </div>
  </form>

  <div id="message"></div>

  <h3>Danh sách câu hỏi</h3>
  <iframe id="questionIframe" src="get_question.php" width="100%" height="400"></iframe>

  <script src="js/question_script.js"></script>

  <script>
    window.addEventListener("message", function (event) {
      if (event.data.type === "fillForm") {
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
          imgPreview.style.display = "none";
        }

        renderMathInPage();
      }
    });
  </script>
</body>
</html>
