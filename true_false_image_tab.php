<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Ảnh minh hoạ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styles_question.css">
  <link rel="stylesheet" href="css/true_false_image_tab.css">
</head>
<body>
  <div class="image-tab-container">
    <h2>🖼️ Ảnh minh hoạ cho câu hỏi</h2>

    <input type="file" id="imageInput" accept="image/*">
    <div id="button-container">
      <button id="deleteImageBtn" style="display: none;">🗑️ Xoá ảnh</button>
    </div>

    <div id="status" class="loading">Chưa có ảnh được chọn.</div>
    <div id="preview"></div>
  </div>

  <script>
    const CLOUDINARY_UPLOAD_PRESET = "<?php echo getenv('CLOUDINARY_UPLOAD_PRESET'); ?>";
    const CLOUDINARY_CLOUD_NAME = "<?php echo getenv('CLOUDINARY_CLOUD_NAME'); ?>";
  </script>
  <script src="js/true_false_image_tab.js"></script>
</body>
</html>
