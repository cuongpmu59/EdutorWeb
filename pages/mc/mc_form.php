<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/main_ui.css">
  <link rel="stylesheet" href="/css/form/form_ui.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
  <div class="form-layout">
    <!-- ===== CỘT TRÁI: FORM NHẬP ===== -->
    <div class="form-left">
      <h2>Nhập câu hỏi trắc nghiệm</h2>

      <!-- Chủ đề -->
      <label>Chủ đề:</label>
      <input type="text" id="topic">

      <!-- Câu hỏi + 👁️ -->
      <label>Câu hỏi: 
        <button class="eye-btn" data-preview="question-preview">👁️</button>
      </label>
      <textarea id="question" rows="3"></textarea>
      <div class="preview-box" id="question-preview"></div>

      <!-- Đáp án A + 👁️ -->
      <div class="answer-row">
        <label>A. 
          <button class="eye-btn" data-preview="a-preview">👁️</button>
        </label>
        <input type="text" id="a">
      </div>
      <div class="preview-box" id="a-preview"></div>

      <!-- Đáp án B + 👁️ -->
      <div class="answer-row">
        <label>B. 
          <button class="eye-btn" data-preview="b-preview">👁️</button>
        </label>
        <input type="text" id="b">
      </div>
      <div class="preview-box" id="b-preview"></div>

      <!-- Đáp án C + 👁️ -->
      <div class="answer-row">
        <label>C. 
          <button class="eye-btn" data-preview="c-preview">👁️</button>
        </label>
        <input type="text" id="c">
      </div>
      <div class="preview-box" id="c-preview"></div>

      <!-- Đáp án D + 👁️ -->
      <div class="answer-row">
        <label>D. 
          <button class="eye-btn" data-preview="d-preview">👁️</button>
        </label>
        <input type="text" id="d">
      </div>
      <div class="preview-box" id="d-preview"></div>

      <!-- Đáp án đúng -->
      <label>Đáp án đúng:</label>
      <select id="correct">
        <option value="">-- Chọn --</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
      </select>

      <!-- Gợi ý -->
      <label>Gợi ý:</label>
      <textarea id="explain" rows="2"></textarea>
    </div>

    <!-- ===== CỘT PHẢI: ẢNH + CHỨC NĂNG ===== -->
    <div class="form-right">
      <!-- Tabs: Nội dung | Ảnh -->
      <div class="tab-header">
        <button class="tab-button active" data-tab="content-tab">📄 Nội dung</button>
        <button class="tab-button" data-tab="image-tab">🖼️ Ảnh minh hoạ</button>
      </div>

      <div class="tab-content active" id="content-tab">
        <label>Xem trước toàn bộ:</label>
        <div class="preview-box" id="full-preview"></div>
      </div>

      <div class="tab-content" id="image-tab">
        <label>Ảnh minh hoạ:</label>
        <input type="file" id="image" accept="image/*">
        <div id="image-preview-container">
          <img id="image-preview" style="display:none;" />
          <button type="button" id="delete-image" style="display:none;">Xoá ảnh</button>
        </div>
      </div>

      <!-- Nhóm nút -->
      <div class="button-group">
        <button id="save-btn">💾 Lưu</button>
        <button id="reset-btn">🔄 Làm mới</button>
        <button id="delete-btn">🗑️ Xoá</button>
        <button id="export-btn">📄 PDF</button>
      </div>
    </div>
  </div>

  <!-- iframe: bảng câu hỏi -->
  <iframe id="question-list" src="get_question.php"></iframe>

  <script src="/js/question_script.js"></script>
</body>
</html>
