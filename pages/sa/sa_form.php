<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>✍️ Câu hỏi trả lời ngắn</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../css/main_ui.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body class="main-layout">

  <div class="tab-bar inner-tabs">
    <button class="tab-button active" data-url="sa_form_inner.php">📝 Nhập câu hỏi</button>
    <button class="tab-button" data-url="sa_image.php">🖼️ Chọn ảnh minh hoạ</button>
    <button class="tab-button" data-url="sa_preview.php">👁️ Xem trước</button>
    <button class="tab-button" data-url="sa_table.php">📋 Danh sách</button>
  </div>

  <iframe id="innerFrame" class="form-iframe" src="sa_form_inner.php" allowfullscreen></iframe>

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
