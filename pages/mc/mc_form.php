<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../dotenv.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>❓ Câu hỏi nhiều lựa chọn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSS tổng hợp -->
  <link rel="stylesheet" href="../../css/main_ui.css">

  <!-- Accent tùy biến -->
  <style>
    :root {
      --accent: #3498db;
    }
  </style>
</head>
<body class="main-layout">

  <!-- Tabs điều hướng bên trong -->
  <div class="tab-bar inner-tabs">
    <button class="tab-button active" data-url="mc_form_inner.php">📝 Nhập câu hỏi</button>
    <button class="tab-button" data-url="mc_image.php">🖼️ Chọn ảnh minh hoạ</button>
    <button class="tab-button" data-url="mc_preview.php">👁️ Xem trước</button>
    <button class="tab-button" data-url="mc_table.php">📋 Danh sách</button>
  </div>

  <!-- Nội dung từng tab sẽ hiển thị ở đây -->
  <div class="tab-content" id="tabContent">
    <!-- Dữ liệu sẽ được nạp động qua JS -->
  </div>

  <!-- MathJax cho công thức Toán học -->
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" defer></script>

  <!-- Bộ điều khiển tải nội dung -->
  <script type="module" src="../../js/modules/controller.js"></script>
</body>
</html>
