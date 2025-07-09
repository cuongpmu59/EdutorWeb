<?php
$current_page = basename($_SERVER['PHP_SELF']); // Xác định trang hiện tại
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📝 Nhập câu hỏi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/main_ui.css">
  <link rel="stylesheet" href="css/main_form.css"> <!-- dùng chung nếu có -->
</head>
<body>

  <h2>📝 Nhập câu hỏi trắc nghiệm</h2>

  <!-- Tabs chuyển trang -->
  <div class="tab-container">
    <a class="tab-button <?= $current_page === 'mc_form.php' ? 'active' : '' ?>" href="mc_form.php">📝 Nhập câu hỏi</a>
    <a class="tab-button <?= $current_page === 'mc_image.php' ? 'active' : '' ?>" href="mc_image.php">🖼️ Ảnh minh hoạ</a>
    <a class="tab-button <?= $current_page === 'mc_preview.php' ? 'active' : '' ?>" href="mc_preview.php">👁️ Xem trước</a>
    <a class="tab-button <?= $current_page === 'mc_table.php' ? 'active' : '' ?>" href="mc_table.php">📋 Danh sách</a>
  </div>

  <!-- Nội dung chính của trang -->
  <div class="form-section">
    <?php require 'mc_form_inner.php'; ?>
  </div>

</body>
</html>
