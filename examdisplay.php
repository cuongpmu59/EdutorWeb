<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Thi Trắc Nghiệm</title>
  <link rel="stylesheet" href="css/styles_Exam.css">
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
        <p>Môn: Toán</p>
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
      <div class="grid">
        
        <!-- CỘT TRÁI: Câu hỏi -->
        <form id="quizForm" class="left-column">
            <?php include 'load_question.php'; ?>
            <button type="button" onclick="submitQuiz()">Nộp bài</button>
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

  <!-- MathJax -->
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script type="text/javascript" id="MathJax-script" async
  src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

   <!-- Tệp JS riêng -->
  <script src="js/script.js"></script>

  <script>
  // Đảm bảo MathJax render toàn bộ sau khi DOM load
  window.addEventListener("DOMContentLoaded", function () {
    if (window.MathJax) {
      MathJax.typeset();
    }
  });
</script>

</body>

</html>