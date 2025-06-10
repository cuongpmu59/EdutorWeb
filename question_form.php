<?php
// question_form.php
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Câu hỏi Trắc nghiệm</title>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    input[type="text"], select, textarea {
      width: 100%;
      padding: 8px;
      box-sizing: border-box;
    }
    .button-group {
      margin-top: 15px;
    }
    .button-group button {
      margin-right: 10px;
      padding: 8px 16px;
    }
    #imagePreview {
      margin-top: 10px;
      max-width: 200px;
      display: none;
      border: 1px solid #ccc;
    }
    iframe {
      width: 100%;
      height: 300px;
      border: 1px solid #ccc;
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <h2>Form Nhập Câu hỏi Trắc nghiệm</h2>

  <form id="questionForm" enctype="multipart/form-data">
    <input type="hidden" id="question_id" name="id">

    <label for="question">Câu hỏi:</label>
    <textarea name="question" id="question" rows="3" required></textarea>

    <label for="answer1">Đáp án A:</label>
    <input type="text" name="answer1" id="answer1" required>

    <label for="answer2">Đáp án B:</label>
    <input type="text" name="answer2" id="answer2" required>

    <label for="answer3">Đáp án C:</label>
    <input type="text" name="answer3" id="answer3" required>

    <label for="answer4">Đáp án D:</label>
    <input type="text" name="answer4" id="answer4" required>

    <label for="correct_answer">Đáp án đúng:</label>
    <select name="correct_answer" id="correct_answer" required>
      <option value="">-- Chọn --</option>
      <option value="A">A</option>
      <option value="B">B</option>
      <option value="C">C</option>
      <option value="D">D</option>
    </select>

    <label for="image">Ảnh minh hoạ (tuỳ chọn):</label>
    <input type="file" name="image" id="image" accept="image/*">
    <img id="imagePreview" src="" alt="Xem ảnh minh hoạ">

    <div class="button-group">
      <button type="button" onclick="saveQuestion()">Lưu</button>
      <button type="button" onclick="deleteQuestion()">Xoá</button>
      <button type="button" onclick="searchQuestion()">Tìm kiếm</button>
      <button type="reset">Xoá trắng</button>
    </div>
  </form>

  <iframe id="questionIframe" src="get_question.php"></iframe>

  <script>
    // Xem trước ảnh
    document.getElementById('image').addEventListener('change', function (e) {
      const file = e.target.files[0];
      const preview = document.getElementById('imagePreview');
      if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
      } else {
        preview.style.display = 'none';
      }
    });
  </script>

  <script src="js/question_script.js"></script>
</body>
</html>
