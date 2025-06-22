<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Thi Trắc Nghiệm</title>
  <link rel="stylesheet" href="css/styles_Exam.css">

  <!-- CẤU HÌNH MATHJAX -->
  <script>
  window.MathJax = {
    tex: {
      inlineMath: [['$', '$'], ['\\(', '\\)']],
      displayMath: [['\\[', '\\]'], ['$$', '$$']],
      processEscapes: true
    },
    options: {
      renderActions: {
        addMenu: [] // Ẩn menu chuột phải
      }
    },
    loader: {
      load: ['input/tex', 'output/chtml']
    }
  };
</script>

  <!-- TẢI THƯ VIỆN MATHJAX -->
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async
    src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
  </script>
</head>

<body>
  <header>
    <div class="container header-grid">
      <!-- Bên trái: thông tin học sinh -->
      <div class="header-left">
        <p>Mã HS: <span id="studentID"></span></p>
        <p>Họ tên: <span id="studentName"></span></p>
        <p>Lớp: <span id="studentClass"></span></p>
      </div>
  
      <!-- Ở giữa: tiêu đề đề thi -->
      <div class="header-center">
        <h1>Đề thi kiểm tra thử</h1>
        <p><strong>Môn:</strong> Toán</p>

      </div>
  
      <!-- Bên phải: thời gian -->
      <div class="header-right">
        <p>Bắt đầu: <span id="startTime"></span></p>
        <p>Còn lại: <span id="countdown"></span></p>
      </div>
    </div>
  </header>
  
  
  <main>
  <div class="container">

    <!-- 🔍 FORM CHỌN CHỦ ĐỀ -->
    <?php
    $selectedTopic = $_GET['topic'] ?? 'Tích phân';
    ?>
    <form method="get" id="topicForm" style="margin-bottom: 20px;">
      <label for="topicSelect"><strong>Chọn chủ đề:</strong></label>
      <select name="topic" id="topicSelect" onchange="document.getElementById('topicForm').submit();">
        <option value="Tích phân" <?= $selectedTopic == 'Tích phân' ? 'selected' : '' ?>>Tích phân</option>
        <option value="Hàm số" <?= $selectedTopic == 'Hàm số' ? 'selected' : '' ?>>Hàm số</option>
        <option value="Lượng giác" <?= $selectedTopic == 'Lượng giác' ? 'selected' : '' ?>>Lượng giác</option>
        <option value="Xác suất" <?= $selectedTopic == 'Xác suất' ? 'selected' : '' ?>>Xác suất</option>
        <!-- Thêm các chủ đề khác nếu cần -->
      </select>
    </form>

    <div class="grid">
      <!-- CỘT TRÁI: Câu hỏi -->
      <form id="quizForm" class="left-column" method="post" novalidate autocomplete="off">
        <!-- 🔵 Thanh tiến trình trả lời -->
        <div id="progressContainer">
          <div id="progressBar">0%</div>
        </div>

        <?php include 'load_question.php'; ?>

        <button type="button" onclick="if(confirm('Bạn có chắc muốn nộp bài?')) submitQuiz();" style="margin-top: 10px;">
          ✅ Nộp bài
        </button>
        <button type="button" id="retryBtn" onclick="resetQuiz()" style="display: none;">
          🔁 Làm lại
        </button>

        <div id="result"></div>
      </form>

      <!-- CỘT PHẢI: Phiếu trả lời -->
      <aside class="right-column">
        <h2>Phiếu trả lời</h2>
        <div class="answer-sheet">
          <!-- Các dòng sẽ được sinh bởi JavaScript -->
        </div>
      </aside>
    </div>
  </div>
</main>


  <footer>
    <div class="container" style="text-align: center; margin-top: 30px; font-size: 14px;">
      © 2025 Hệ thống thi trắc nghiệm Toán học
    </div>
  </footer>

  <!-- Tệp JS riêng -->
  <script src="js/script.js"></script>

  <script>
  // Đảm bảo MathJax render toàn bộ sau khi DOM load
  window.addEventListener("DOMContentLoaded", function () {
  if (window.MathJax) {
    MathJax.startup.promise.then(() => {
      MathJax.typesetPromise();
    });
  }
});

</script>

</body>

</html>