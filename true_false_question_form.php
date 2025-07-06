<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi đúng/sai</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
    }

    .tabs {
      display: flex;
      background-color: #3498db;
      overflow-x: auto;
    }

    .tab-button {
      flex: 1;
      padding: 10px;
      text-align: center;
      background: #3498db;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s;
    }

    .tab-button:hover,
    .tab-button.active {
      background-color: #2980b9;
    }

    .tab-content {
      display: none;
      padding: 20px;
      background-color: var(--bg-light, #f9f9f9);
    }

    .tab-content.active {
      display: block;
    }

    iframe {
      width: 100%;
      height: 700px;
      border: none;
    }
  </style>
</head>
<body>
  <h2 style="text-align:center; padding: 10px;">🧠 Quản lý câu hỏi đúng/sai</h2>

  <!-- Tabs -->
  <div class="tabs">
    <button class="tab-button active" onclick="showTab(0)">📝 Nhập câu hỏi</button>
    <button class="tab-button" onclick="showTab(1)">🖼️ Ảnh minh hoạ</button>
    <button class="tab-button" onclick="showTab(2)">👁️ Xem trước</button>
    <button class="tab-button" onclick="showTab(3)">📋 Danh sách câu hỏi</button>
  </div>

  <!-- Tab content -->
  <div class="tab-content active"><?php include 'true_false_question_form_inner.php'; ?></div>
  <div class="tab-content"><?php include 'true_false_image_tab.php'; ?></div>
  <div class="tab-content"><iframe src="preview_true_false_question.php"></iframe></div>
  <div class="tab-content"><iframe src="get_true_false_questions.php"></iframe></div>

  <!-- Tab logic -->
  <script>
    function showTab(index) {
      const buttons = document.querySelectorAll('.tab-button');
      const contents = document.querySelectorAll('.tab-content');
      buttons.forEach((btn, i) => {
        btn.classList.toggle('active', i === index);
        contents[i].classList.toggle('active', i === index);
      });
    }
  </script>
</body>
</html>
