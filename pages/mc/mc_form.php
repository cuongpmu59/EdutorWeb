<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/main_ui.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
  <div class="form-container">

    <!-- === CỘT TRÁI: FORM CÂU HỎI === -->
    <div class="column-left">
      <h2>Nhập câu hỏi trắc nghiệm</h2>

      <label>Chủ đề:</label>
      <input type="text" id="topic" placeholder="Nhập chủ đề...">

      <label>Câu hỏi:</label>
      <div class="input-with-eye">
        <textarea id="question" placeholder="Nhập nội dung câu hỏi..."></textarea>
        <button class="toggle-preview" onclick="togglePreview('question')">👁️</button>
      </div>
      <div id="preview-question" class="preview-box hidden"></div>

      <div class="answer-row">
  <label for="answerA">A</label>
  <button type="button" class="eye-button" data-target="answerA">👁️</button>
  <input type="text" id="answerA" name="answerA" required>
  <span class="preview answer-preview" id="preview-answerA"></span>
</div>

<div class="answer-row">
  <label for="answerB">B</label>
  <button type="button" class="eye-button" data-target="answerB">👁️</button>
  <input type="text" id="answerB" name="answerB" required>
  <span class="preview answer-preview" id="preview-answerB"></span>
</div>

<div class="answer-row">
  <label for="answerC">C</label>
  <button type="button" class="eye-button" data-target="answerC">👁️</button>
  <input type="text" id="answerC" name="answerC" required>
  <span class="preview answer-preview" id="preview-answerC"></span>
</div>

<div class="answer-row">
  <label for="answerD">D</label>
  <button type="button" class="eye-button" data-target="answerD">👁️</button>
  <input type="text" id="answerD" name="answerD" required>
  <span class="preview answer-preview" id="preview-answerD"></span>
</div>


      <label>Đáp án đúng:</label>
      <select id="correctAnswer">
        <option value="">-- Chọn --</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
      </select>

      <div class="preview-toggle-all">
        <button onclick="toggleAllPreviews()">👁️ Xem toàn bộ</button>
      </div>
      <div id="preview-all" class="preview-box hidden"></div>
    </div>

    <!-- === CỘT PHẢI: ẢNH & NÚT === -->
    <div class="column-right">

      <!-- KHU VỰC ẢNH MINH HOẠ -->
      <div class="image-section">
        <label>Ảnh minh hoạ:</label>
        <img id="imagePreview" src="" alt="Xem trước ảnh" class="image-preview hidden">
        <input type="file" id="imageInput" accept="image/*">
        <div class="image-buttons">
          <button onclick="uploadImage()">📤 Tải ảnh</button>
          <button onclick="deleteImage()">🗑️ Xoá ảnh</button>
        </div>
      </div>

      <!-- KHU VỰC NÚT CHỨC NĂNG -->
      <div class="action-buttons">
        <button onclick="saveQuestion()">💾 Lưu</button>
        <button onclick="resetForm()">🔄 Làm lại</button>
        <button onclick="deleteQuestion()">❌ Xoá</button>
        <button onclick="viewTable()">📋 Xem bảng</button>
      </div>
    </div>
  </div>

  <script>
    function togglePreview(type) {
      const inputId = type === 'question' ? 'question' : 'answer' + type;
      const previewId = 'preview-' + (type === 'question' ? 'question' : type);
      const previewBox = document.getElementById(previewId);
      const content = document.getElementById(inputId).value;
      previewBox.innerHTML = content;
      previewBox.classList.toggle('hidden');
      MathJax.typesetPromise();
    }

    function toggleAllPreviews() {
      const fields = ['question', 'A', 'B', 'C', 'D'];
      let allContent = '<strong>Câu hỏi:</strong><br>' + document.getElementById('question').value + '<br><br>';
      fields.slice(1).forEach(letter => {
        allContent += `<strong>Đáp án ${letter}:</strong> ${document.getElementById('answer' + letter).value}<br>`;
      });
      const previewAll = document.getElementById('preview-all');
      previewAll.innerHTML = allContent;
      previewAll.classList.toggle('hidden');
      MathJax.typesetPromise();
    }

    function uploadImage() {
      alert('Đang phát triển: Tải ảnh lên máy chủ');
    }

    function deleteImage() {
      document.getElementById('imageInput').value = '';
      document.getElementById('imagePreview').src = '';
      document.getElementById('imagePreview').classList.add('hidden');
    }

    function saveQuestion() {
      alert('Đang lưu câu hỏi...');
    }

    function resetForm() {
      document.querySelectorAll('input, textarea, select').forEach(el => el.value = '');
      document.querySelectorAll('.preview-box').forEach(el => el.classList.add('hidden'));
      deleteImage();
    }

    function deleteQuestion() {
      alert('Bạn muốn xoá câu hỏi này?');
    }

    function viewTable() {
      window.location.href = 'mc_table.php';
    }
  </script>
</body>
</html>
