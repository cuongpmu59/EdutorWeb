<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>
  <link rel="stylesheet" href="css/main_ui.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body>

<h2>📋 Quản lý câu hỏi</h2>

<!-- Tabs chính -->
<div class="tab-container">
  <div class="tab-button active" data-tab="tab-mc">📝 Trắc nghiệm nhiều lựa chọn</div>
  <div class="tab-button" data-tab="tab-tf">✅ Trắc nghiệm Đúng/Sai</div>
  <div class="tab-button" data-tab="tab-sa">✍️ Trắc nghiệm trả lời ngắn</div>
</div>

<!-- Tab nội dung -->
<div class="tab-content active" id="tab-mc">
  <?php require 'pages/mc/mc_form.php'; ?>
</div>
<div class="tab-content" id="tab-tf">
  <?php require 'pages/tf/tf_form.php'; ?>
</div>
<div class="tab-content" id="tab-sa">
  <?php require 'pages/sa/sa_form.php'; ?>
</div>

<!-- Biến môi trường Cloudinary -->
<script>
  const CLOUDINARY_CLOUD_NAME = "<?= env('CLOUDINARY_CLOUD_NAME') ?>";
  const CLOUDINARY_UPLOAD_PRESET = "<?= env('CLOUDINARY_UPLOAD_PRESET') ?>";
</script>

<!-- Scripts -->
<script type="module" src="js/modules/controller.js"></script>

</body>
</html>
