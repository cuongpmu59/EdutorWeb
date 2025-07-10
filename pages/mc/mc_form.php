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

  <!-- Tabs điều hướng -->
  <div class="tab-bar inner-tabs">
    <button class="tab-button active" data-url="mc_form_inner.php">📝 Nhập câu hỏi</button>
    <button class="tab-button" data-url="mc_image.php">🖼️ Chọn ảnh minh hoạ</button>
    <button class="tab-button" data-url="mc_preview.php">👁️ Xem trước</button>
    <button class="tab-button" data-url="mc_table.php">📋 Danh sách</button>
  </div>

  <!-- Khu vực hiển thị nội dung từng tab qua iframe -->
  <iframe id="innerFrame" class="form-iframe" src="mc_form_inner.php" allowfullscreen></iframe>

  <script>
    const buttons = document.querySelectorAll(".tab-button");
    const iframe = document.getElementById("innerFrame");

    buttons.forEach(button => {
      button.addEventListener("click", () => {
        buttons.forEach(btn => btn.classList.remove("active"));
        button.classList.add("active");
        iframe.src = button.getAttribute("data-url");
      });
    });
  </script>

</body>
</html>
