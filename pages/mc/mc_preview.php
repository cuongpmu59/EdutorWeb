<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>👁️ Xem trước</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="css/main_ui.css">
  <link rel="stylesheet" href="css/form.css">
  <link rel="stylesheet" href="css/buttons.css">
  <link rel="stylesheet" href="css/tabs.css">
  <link rel="stylesheet" href="css/preview.css">

  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: var(--bg-light, #f9f9f9);
      color: var(--color-dark, #333);
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 960px;
      margin: 40px auto;
      padding: 20px;
      background-color: white;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    h2 {
      text-align: center;
      color: var(--accent, #3498db);
      margin-bottom: 30px;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>👁️ Xem trước câu hỏi</h2>

    <div class="tab-container">
      <a class="tab-button <?= $current_page === 'mc_form.php' ? 'active' : '' ?>" href="mc_form.php">📝 Nhập câu hỏi</a>
      <a class="tab-button <?= $current_page === 'mc_image.php' ? 'active' : '' ?>" href="mc_image.php">🖼️ Ảnh minh hoạ</a>
      <a class="tab-button <?= $current_page === 'mc_preview.php' ? 'active' : '' ?>" href="mc_preview.php">👁️ Xem trước</a>
      <a class="tab-button <?= $current_page === 'mc_table.php' ? 'active' : '' ?>" href="mc_table.php">📋 Danh sách</a>
    </div>

    <div class="form-section">
      <?php require 'mc_preview_inner.php'; ?>
    </div>
  </div>

</body>
</html>
