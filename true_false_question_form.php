<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi đúng/sai</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <link rel="stylesheet" href="css/true_false_form.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body>
  <h2 style="text-align:center; padding: 10px;">🧠 Quản lý câu hỏi đúng/sai</h2>

  <div class="tabs">
    <button class="tab-button active" data-src="true_false_question_form_inner.php">📝 Nhập câu hỏi</button>
    <button class="tab-button" data-src="true_false_image_tab.php">🖼️ Ảnh minh hoạ</button>
    <button class="tab-button" data-src="preview_true_false_question.php">👁️ Xem trước</button>
    <button class="tab-button" data-src="get_true_false_questions.php">📋 Danh sách</button>
  </div>

  <div class="iframe-container">
    <iframe id="contentFrame" src="true_false_question_form_inner.php"></iframe>
  </div>

  <script src="js/true_false_form.js"></script>
</body>
</html>
