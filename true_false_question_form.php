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
    <button class="tab-button active" onclick="loadTab(0)">📝 Nhập câu hỏi</button>
    <button class="tab-button" onclick="loadTab(1)">🖼️ Ảnh minh hoạ</button>
    <button class="tab-button" onclick="loadTab(2)">👁️ Xem trước</button>
    <button class="tab-button" onclick="loadTab(3)">📋 Danh sách câu hỏi</button>
  </div>

  <div class="iframe-container">
    <iframe id="contentFrame" src="true_false_question_form_inner.php"></iframe>
  </div>

  <script src="js/true_false_form.js"></script>
</body>
</html>
