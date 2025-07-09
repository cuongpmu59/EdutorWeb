<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📝 Câu hỏi trắc nghiệm</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Giao diện -->
  <link rel="stylesheet" href="css/main_ui.css">
  <link rel="stylesheet" href="css/form.css">
  <link rel="stylesheet" href="css/buttons.css">
  <link rel="stylesheet" href="css/tabs.css">

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: var(--bg-light, #f4f4f4);
      margin: 0;
      padding: 0;
      color: var(--color-dark, #333);
    }

    .container {
      max-width: 960px;
      margin: 0 auto;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: var(--accent, #3498db);
      margin: 20px 0;
    }

    .tab-container {
      display: flex;
      width: 100%;
      border-radius: 8px;
      overflow: hidden;
      margin-bottom: 20px;
    }

    .tab-button {
      flex: 1;
      text-align: center;
      padding: 14px 0;
      text-decoration: none;
      font-weight: bold;
      border: none;
      background-color: #e0e0e0;
      color: #444;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .tab-button:hover {
      background-color: #d0d0d0;
    }

    .tab-button.active {
      background-color: var(--accent, #3498db);
      color: white;
    }

    .tab-content {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.08);
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>📝 Câu hỏi trắc nghiệm</h2>

    <!-- Tabs -->
    <div class="tab-container">
      <a class="tab-button <?= $current_page === 'mc_form.php' ? 'active' : '' ?>" href="mc_form.php">📝 Nhập số liệu</a>
      <a class="tab-button <?= $current_page === 'mc_image.php' ? 'active' : '' ?>" href="mc_image.php">🖼️ Ảnh minh hoạ</a>
      <a class="tab-button <?= $current_page === 'mc_preview.php' ? 'active' : '' ?>" href="mc_preview.php">👁️ Xem trước</a>
      <a class="tab-button <?= $current_page === 'mc_table.php' ? 'active' : '' ?>" href="mc_table.php">📋 Danh sách</a>
    </div>

    <!-- Nội dung chính -->
    <div class="tab-content">
      <?php require 'mc_form_inner.php'; ?>
    </div>
  </div>

</body>
</html>
