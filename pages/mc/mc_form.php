<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>❓ Câu hỏi nhiều lựa chọn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../css/main_ui.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
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
    const buttons = document.querySelectorAll(".inner-tabs .tab-button");
    const iframe = document.getElementById("innerFrame");

    buttons.forEach(button => {
      button.addEventListener("click", () => {
        buttons.forEach(b => b.classList.remove("active"));
        button.classList.add("active");
        iframe.src = button.getAttribute("data-url");
      });
    });
  </script>

</body>
</html>
