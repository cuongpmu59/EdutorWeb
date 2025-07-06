<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body>

<h2>📋 Quản lý câu hỏi trắc nghiệm</h2>

<!-- Tabs -->
<div class="tabs">
  <div class="tab-btn active" data-tab="tab-form">📝 Nhập câu hỏi</div>
  <div class="tab-btn" data-tab="tab-preview">👁️ Xem trước</div>
  <div class="tab-btn" data-tab="tab-image">🖼️ Ảnh minh hoạ</div>
</div>

<!-- Tab 1: Nhập -->
<div class="tab-content active" id="tab-form">
  <form id="questionForm">
    <input type="file" id="image" name="image" style="display:none;">
    <input type="hidden" name="id" id="question_id">
    <input type="hidden" name="image_url" id="image_url">

    <label>Chủ đề:</label>
    <input type="text" name="topic" id="topic">

    <label>Câu hỏi:</label>
    <textarea name="question" id="question" rows="3"></textarea>

    <label>Đáp án A:</label>
    <input type="text" name="answer1" id="answer1">
    <label>Đáp án B:</label>
    <input type="text" name="answer2" id="answer2">
    <label>Đáp án C:</label>
    <input type="text" name="answer3" id="answer3">
    <label>Đáp án D:</label>
    <input type="text" name="answer4" id="answer4">

    <label>Đáp án đúng:</label>
    <select name="correct_answer" id="correct_answer">
      <option value="">-- Chọn đáp án đúng --</option>
      <option value="A">A</option>
      <option value="B">B</option>
      <option value="C">C</option>
      <option value="D">D</option>
    </select>

    <div style="margin-top:15px;">
      <button type="submit" class="btn-primary">💾 Lưu</button>
      <button type="button" class="btn-secondary" id="resetBtn">🔄 Làm mới</button>
      <button type="button" class="btn-danger" id="deleteBtn">🗑️ Xoá</button>
      <button type="button" class="btn-secondary" id="exportPdfBtn">📝 Xuất đề PDF</button>
    </div>
  </form>
</div>

<!-- Tab 2: Xem trước -->
<div class="tab-content" id="tab-preview">
  <div id="preview_area"><em>⚡ Nội dung xem trước sẽ hiển thị tại đây...</em></div>
  <img id="preview_image" style="display:none; max-height: 150px; margin-top: 10px; border: 1px solid #ccc;">
</div>

<!-- Tab 3: Ảnh -->
<div class="tab-content" id="tab-image">
  <p><strong>Ảnh minh hoạ hiện tại:</strong></p>
  <img id="imageTabPreview" style="max-height: 150px; border: 1px solid #ccc; display: none;">
  <div id="imageTabFileName" style="color: gray; font-style: italic;"></div>
  <button type="button" class="btn-danger" id="delete_image_tab" style="display:none;">🗑️ Xoá ảnh</button>
  <button type="button" class="btn-secondary" id="select_image_tab">📂 Chọn ảnh</button>
</div>

<!-- Iframe -->
<iframe id="questionIframe" src="get_question.php" width="100%" height="500" style="margin-top:30px; border: 1px solid #aaa;"></iframe>

<!-- Biến môi trường -->
<script>
  const CLOUDINARY_CLOUD_NAME = "<?= env('CLOUDINARY_CLOUD_NAME') ?>";
  const CLOUDINARY_UPLOAD_PRESET = "<?= env('CLOUDINARY_UPLOAD_PRESET') ?>";
</script>

<script src="js/question_script.js"></script>

<script>
  // Tabs
  document.querySelectorAll(".tab-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      const tabId = btn.getAttribute("data-tab");
      document.querySelectorAll(".tab-content").forEach(tab => tab.classList.remove("active"));
      document.getElementById(tabId).classList.add("active");
    });
  });
</script>

</body>
</html>
