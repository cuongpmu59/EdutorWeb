<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/main_ui.css">
  <link rel="stylesheet" href="css/main_form.css"> <!-- 👈 Thêm dòng này -->
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body>

  <h2>📋 Quản lý câu hỏi</h2>

  <div class="tab-container">
    <a class="tab-button" href="pages/mc/mc_form.php">📝 Trắc nghiệm nhiều lựa chọn</a>
    <a class="tab-button" href="pages/tf/tf_form.php">✅ Trắc nghiệm Đúng/Sai</a>
    <a class="tab-button" href="pages/sa/sa_form.php">✍️ Trắc nghiệm Trả lời ngắn</a>
  </div>

  <script>
    const CLOUDINARY_CLOUD_NAME = "<?= env('CLOUDINARY_CLOUD_NAME') ?>";
    const CLOUDINARY_UPLOAD_PRESET = "<?= env('CLOUDINARY_UPLOAD_PRESET') ?>";
  </script>

</body>
</html>
