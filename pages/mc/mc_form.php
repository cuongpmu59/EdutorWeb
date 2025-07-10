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
  <link rel="stylesheet" href="../../css/main_ui.css">
  <style>
    :root { --accent: #3498db; } /* Xanh dương */
  </style>
</head>
<body class="main-layout">

  <div class="tab-bar inner-tabs">
    <a class="tab-button active" href="mc_form.php">📝 Nhập câu hỏi</a>
    <a class="tab-button" href="mc_image.php">🖼️ Chọn ảnh minh hoạ</a>
    <a class="tab-button" href="mc_preview.php">👁️ Xem trước</a>
    <a class="tab-button" href="mc_table.php">📋 Danh sách</a>
  </div>

  <div class="tab-content">
    <?php require_once __DIR__ . '/mc_form_inner.php'; ?>
  </div>

</body>
</html>
