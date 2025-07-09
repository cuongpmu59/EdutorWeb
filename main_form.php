<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📋 Quản lý câu hỏi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSS -->
  <link rel="stylesheet" href="css/main_ui.css">
  <link rel="stylesheet" href="css/form.css">
  <link rel="stylesheet" href="css/buttons.css">
  <link rel="stylesheet" href="css/tabs.css">

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: var(--bg-light, #f9f9f9);
      color: var(--color-dark, #333);
    }

    .container {
      max-width: 1200px;
      margin: 30px auto;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: var(--accent, #3498db);
    }

    .tab-container {
      display: flex;
      width: 100%;
      border-bottom: 2px solid var(--accent);
    }

    .tab-button {
      flex: 1;
      padding: 14px;
      text-align: center;
      font-weight: bold;
      color: var(--accent);
      background: var(--bg-light, #f1f1f1);
      cursor: pointer;
      border: none;
      transition: background-color 0.3s ease;
    }

    .tab-button.active {
      background-color: var(--accent);
      color: white;
    }

    .tab-content {
      display: none;
      margin-top: 20px;
    }

    .tab-content.active {
      display: block;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>📋 Quản lý câu hỏi trắc nghiệm</h2>

    <!-- Tabs chính -->
    <div class="tab-container">
      <button class="tab-button active" data-tab="tab-mc">📝 Trắc nghiệm nhiều lựa chọn</button>
      <button class="tab-button" data-tab="tab-tf">✅ Trắc nghiệm Đúng/Sai</button>
      <button class="tab-button" data-tab="tab-sa">✍️ Câu hỏi ngắn</button>
    </div>

    <!-- Nội dung từng tab -->
    <div class="tab-content active" id="tab-mc">
      <?php require 'pages/mc/mc_form.php'; ?>
    </div>

    <div class="tab-content" id="tab-tf">
      <?php require 'pages/tf/tf_form.php'; ?>
    </div>

    <div class="tab-content" id="tab-sa">
      <?php require 'pages/sa/sa_form.php'; ?>
    </div>
  </div>

  <!-- Script xử lý tabs -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const tabButtons = document.querySelectorAll(".tab-button");
      const tabContents = document.querySelectorAll(".tab-content");

      tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
          const target = btn.getAttribute("data-tab");

          tabButtons.forEach(b => b.classList.remove("active"));
          tabContents.forEach(c => c.classList.remove("active"));

          btn.classList.add("active");
          document.getElementById(target).classList.add("active");
        });
      });
    });
  </script>

</body>
</html>
