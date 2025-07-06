<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi đúng/sai</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: var(--bg-light, #f4f4f4);
    }

    .tab-container {
      display: flex;
      flex-direction: column;
      height: 100vh;
    }

    .tab-header {
      display: flex;
      background-color: #3498db;
      overflow-x: auto;
    }

    .tab-header button {
      flex: 1;
      padding: 12px;
      background: #3498db;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
      white-space: nowrap;
    }

    .tab-header button:hover,
    .tab-header button.active {
      background: #2980b9;
    }

    .tab-content {
      flex: 1;
      border-top: 1px solid #ccc;
    }

    .tab-content iframe {
      width: 100%;
      height: 100%;
      border: none;
    }
  </style>
</head>
<body>
  <div class="tab-container">
    <div class="tab-header">
      <button class="tab-button active" onclick="switchTab(0)">📝 Nhập câu hỏi</button>
      <button class="tab-button" onclick="switchTab(1)">🖼️ Ảnh minh hoạ</button>
      <button class="tab-button" onclick="switchTab(2)">👁️ Xem trước toàn bộ</button>
      <button class="tab-button" onclick="switchTab(3)">📋 Danh sách câu hỏi</button>
    </div>

    <div class="tab-content">
      <iframe id="tabFrame" src="true_false_question_form_inner.php"></iframe>
    </div>
  </div>

  <script>
    const tabs = [
      'true_false_question_form_inner.php',
      'true_false_image_tab.php',
      'true_false_preview.php',
      'get_true_false_questions.php'
    ];

    function switchTab(index) {
      document.querySelectorAll('.tab-button').forEach((btn, i) => {
        btn.classList.toggle('active', i === index);
      });
      document.getElementById('tabFrame').src = tabs[index];
    }
  </script>
</body>
</html>
