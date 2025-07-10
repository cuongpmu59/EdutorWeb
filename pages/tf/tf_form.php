<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../dotenv.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>✔️ Câu hỏi Đúng/Sai</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../css/main_ui.css">
  <style>
    :root { --accent: #27ae60; } /* Xanh lá */
  </style>
</head>
<body class="main-layout">

  <div class="tab-bar inner-tabs">
    <a class="tab-button active" href="tf_form.php">📝 Nhập câu hỏi</a>
    <a class="tab-button" href="tf_image.php">🖼️ Chọn ảnh minh hoạ</a>
    <a class="tab-button" href="tf_preview.php">👁️ Xem trước</a>
    <a class="tab-button" href="tf_table.php">📋 Danh sách</a>
  </div>

  <div class="tab-content">
    <?php require_once __DIR__ . '/tf_form_inner.php'; ?>
  </div>

</body>
</html>
