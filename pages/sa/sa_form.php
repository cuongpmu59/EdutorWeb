<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../dotenv.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>✍️ Câu hỏi trả lời ngắn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../css/main_ui.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body class="main-layout">

  <!-- Tabs điều hướng -->
  <div class="tab-bar inner-tabs">
    <a class="tab-button active" href="sa_form.php">📝 Nhập câu hỏi</a>
    <a class="tab-button" href="sa_image.php">🖼️ Chọn ảnh minh hoạ</a>
    <a class="tab-button" href="sa_preview.php">👁️ Xem trước</a>
    <a class="tab-button" href="sa_table.php">📋 Danh sách</a>
  </div>

  <!-- Nội dung form nhúng trực tiếp -->
  <div class="tab-content">
    <?php require_once __DIR__ . '/sa_form_inner.php'; ?>
  </div>

</body>
</html>
