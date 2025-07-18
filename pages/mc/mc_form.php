<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/main_ui.css">
</head>
<body>
  <div class="form-container">
    <h2>Nhập câu hỏi trắc nghiệm <button class="eye-btn" onclick="previewFull()">👁️</button></h2>

    <form id="question-form">
      <div class="column-left">
        <div class="form-group">
          <label for="topic">Chủ đề:</label>
          <input type="text" id="topic" name="topic" required>
        </div>

        <div class="form-group">
          <label for="question">Câu hỏi: <button class="eye-btn" onclick="previewField('question')">👁️</button></label>
          <textarea id="question" name="question" required></textarea>
        </div>

        <div class="form-group answer-row">
          <label for="answerA">A: <button class="eye-btn" onclick="previewField('answerA')">👁️</button></label>
          <input type="text" id="answerA" name="answerA" required>
        </div>

        <div class="form-group answer-row">
          <label for="answerB">B: <button class="eye-btn" onclick="previewField('answerB')">👁️</button></label>
          <input type="text" id="answerB" name="answerB" required>
        </div>

        <div class="form-group answer-row">
          <label for="answerC">C: <button class="eye-btn" onclick="previewField('answerC')">👁️</button></label>
          <input type="text" id="answerC" name="answerC" required>
        </div>

        <div class="form-group answer-row">
          <label for="answerD">D: <button class="eye-btn" onclick="previewField('answerD')">👁️</button></label>
          <input type="text" id="answerD" name="answerD" required>
        </div>

        <div class="form-group">
          <label for="correct">Đáp án đúng:</label>
          <select id="correct" name="correct" required>
            <option value="">--Chọn--</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
          </select>
        </div>
      </div>

      <div class="column-right">
        <div class="image-section">
          <label>Ảnh minh hoạ:</label>
          <div class="image-preview" id="imagePreview">Chưa có ảnh</div>
          <input type="file" id="imageInput" accept="image/*" hidden>
          <div class="button-group image-buttons">
            <button type="button" onclick="document.getElementById('imageInput').click()">📤 Tải ảnh</button>
            <button type="button" onclick="removeImage()">❌ Xoá ảnh</button>
          </div>
        </div>

        <div class="button-group form-buttons">
          <button type="submit">💾 Lưu</button>
          <button type="button" onclick="resetForm()">🔄 Làm lại</button>
          <button type="button" onclick="deleteQuestion()">🗑️ Xoá</button>
          <button type="button" onclick="viewTable()">📋 Xem bảng</button>
        </div>
      </div>
    </form>

    <div id="preview-container" class="preview-box" style="display:none;">
      <h3>Xem trước nội dung</h3>
      <div id="preview-content"></div>
    </div>
  </div>

  <script src="/js/mc_form.js"></script>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async
    src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
  </script>
</body>
</html>
