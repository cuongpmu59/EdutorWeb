<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
  <style>
    body { font-family: Arial; padding: 10px; max-width: 900px; margin: auto; }
    .tabs { display: flex; margin-bottom: 10px; }
    .tab-btn {
      flex: 1; text-align: center; padding: 10px;
      border: 1px solid #ccc; border-bottom: none; cursor: pointer;
      background: #f0f0f0;
    }
    .tab-btn.active {
      background: #fff; font-weight: bold; border-bottom: 1px solid #fff;
    }
    .tab-content { border: 1px solid #ccc; padding: 15px; }
    label { font-weight: bold; display: block; margin-top: 10px; }
    input[type="text"], select, textarea {
      width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 5px;
    }
    textarea { resize: vertical; }
    button { margin-top: 12px; margin-right: 10px; padding: 8px 14px; border: none; border-radius: 6px; cursor: pointer; }
    .btn-primary { background-color: #007bff; color: white; }
    .btn-danger { background-color: #dc3545; color: white; }
    .btn-secondary { background-color: #6c757d; color: white; }
    #preview_image { max-height: 120px; margin-top: 10px; display: none; border: 1px solid #aaa; }
    #delete_image { display: none; margin-top: 5px; }
    #preview_area { padding-top: 10px; }
  </style>
</head>
<body>

<h2>📋 Quản lý câu hỏi trắc nghiệm</h2>

<div class="tabs">
  <div class="tab-btn active" data-tab="form">📝 Nhập câu hỏi</div>
  <div class="tab-btn" data-tab="preview">👁️ Xem trước</div>
</div>

<div class="tab-content" id="tab-form">
  <form id="questionForm">
    <input type="hidden" name="id" id="question_id">

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

    <label>Ảnh minh hoạ:</label>
    <input type="hidden" name="image_url" id="image_url">
    <input type="file" id="image" accept="image/*" style="display:none">
    <button type="button" class="btn-secondary" id="select_image">📷 Chọn ảnh</button>
    <img id="preview_image">
    <button type="button" class="btn-danger" id="delete_image" data-delete="0">🗑️ Xoá ảnh</button>

    <div style="margin-top:15px;">
      <button type="submit" class="btn-primary">💾 Lưu</button>
      <button type="button" class="btn-secondary" id="resetBtn">🔄 Làm mới</button>
      <button type="button" class="btn-danger" id="deleteBtn">🗑️ Xoá</button>
      <button type="button" class="btn-secondary" id="exportPdfBtn">📝 Xuất đề PDF</button>
    </div>

    <div style="margin-top:15px">
      <label><input type="checkbox" id="toggle_preview_question" checked> Xem trước câu hỏi</label>
      <label><input type="checkbox" id="toggle_preview_answers" checked> Xem trước đáp án</label>
      <label><input type="checkbox" id="toggle_preview_all" checked> Xem trước toàn bộ</label>
    </div>
  </form>
</div>

<div class="tab-content" id="tab-preview" style="display:none">
  <div id="preview_area"><em>⚡ Nội dung xem trước sẽ hiển thị tại đây...</em></div>
</div>

<iframe id="questionIframe" src="get_question.php" width="100%" height="500" style="margin-top:30px;border:1px solid #aaa;"></iframe>

<script src="js/question_script.js"></script>
<script>
  // Tab switching
  const tabButtons = document.querySelectorAll(".tab-btn");
  const tabForm = document.getElementById("tab-form");
  const tabPreview = document.getElementById("tab-preview");

  tabButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      tabButtons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");

      const tab = btn.dataset.tab;
      tabForm.style.display = tab === "form" ? "block" : "none";
      tabPreview.style.display = tab === "preview" ? "block" : "none";
    });
  });
</script>
</body>
</html>
