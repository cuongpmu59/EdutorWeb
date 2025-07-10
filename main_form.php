<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🧾 Quản lý Ngân hàng Câu hỏi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Giao diện tổng hợp từ nhiều CSS modules -->
  <link rel="stylesheet" href="css/main_ui.css">
</head>
<body class="main-layout">

  <!-- Thanh Tab điều hướng -->
  <div class="tab-bar">
    <button class="tab-button active" data-url="pages/mc/mc_form.php">❓ Trắc nghiệm nhiều lựa chọn</button>
    <button class="tab-button" data-url="pages/tf/tf_form.php">✔️ Trắc nghiệm Đúng/Sai</button>
    <button class="tab-button" data-url="pages/sa/sa_form.php">✍️ Trắc nghiệm trả lời ngắn</button>
  </div>

  <!-- Iframe chứa form tương ứng -->
  <iframe id="formFrame" class="form-iframe" src="mc_form.php" allowfullscreen></iframe>

  <!-- Script chuyển tab và đổi iframe -->
  <script>
    const tabs = document.querySelectorAll(".tab-button");
    const iframe = document.getElementById("formFrame");

    tabs.forEach(tab => {
      tab.addEventListener("click", () => {
        tabs.forEach(t => t.classList.remove("active")); // Xoá active
        tab.classList.add("active");                    // Thêm active mới
        iframe.src = tab.getAttribute("data-url");      // Đổi URL iframe
      });
    });
  </script>

</body>
</html>
