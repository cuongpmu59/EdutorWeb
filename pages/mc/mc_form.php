<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/mc_form.css">
</head>
<body>
  <div class="form-container">
    <!-- CỘT TRÁI: NHẬP CÂU HỎI -->
    <div class="column-left">
      <h2>Nhập câu hỏi trắc nghiệm</h2>

      <label>Chủ đề:</label>
      <input type="text" id="topic">

      <label>Câu hỏi:</label>
      <div class="input-preview-row">
        <textarea id="question"></textarea>
        <button class="eye-button" onclick="togglePreview('question')">👁️</button>
      </div>
      <div class="preview-box" id="preview-question"></div>

      <label>Đáp án A:</label>
      <div class="input-preview-row">
        <input type="text" id="answerA">
        <button class="eye-button" onclick="togglePreview('answerA')">👁️</button>
      </div>
      <div class="preview-box" id="preview-answerA"></div>

      <label>Đáp án B:</label>
      <div class="input-preview-row">
        <input type="text" id="answerB">
        <button class="eye-button" onclick="togglePreview('answerB')">👁️</button>
      </div>
      <div class="preview-box" id="preview-answerB"></div>

      <label>Đáp án C:</label>
      <div class="input-preview-row">
        <input type="text" id="answerC">
        <button class="eye-button" onclick="togglePreview('answerC')">👁️</button>
      </div>
      <div class="preview-box" id="preview-answerC"></div>

      <label>Đáp án D:</label>
      <div class="input-preview-row">
        <input type="text" id="answerD">
        <button class="eye-button" onclick="togglePreview('answerD')">👁️</button>
      </div>
      <div class="preview-box" id="preview-answerD"></div>

      <label>Đáp án đúng:</label>
      <select id="correctAnswer">
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
      </select>
    </div>

    <!-- CỘT PHẢI: ẢNH + NÚT -->
    <div class="column-right">
      <!-- KHU VỰC ẢNH -->
      <div class="image-area">
        <label>Ảnh minh hoạ:</label>
        <div class="image-preview" id="imagePreview">Chưa có ảnh</div>
        <input type="file" id="imageInput" accept="image/*" hidden>
        <div class="image-buttons">
          <button onclick="document.getElementById('imageInput').click()">📁 Tải ảnh</button>
          <button onclick="removeImage()">🗑️ Xoá ảnh</button>
        </div>
      </div>

      <!-- KHU VỰC NÚT FORM -->
      <div class="form-buttons">
        <button onclick="saveQuestion()">💾 Lưu</button>
        <button onclick="resetForm()">🔁 Làm lại</button>
        <button onclick="deleteQuestion()">🗑️ Xoá</button>
        <button onclick="window.location.href='mc_table.php'">📋 Xem bảng</button>
      </div>
    </div>
  </div>

  <!-- MathJax -->
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async
    src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
  </script>

  <script src="/js/form/mc_form.js"></script>
</body>
</html>
