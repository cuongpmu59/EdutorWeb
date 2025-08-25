<?php
// exam_form.php
require_once __DIR__ . '/../../includes/db_connection.php'; // Kết nối PDO

// Lấy 20 câu hỏi ngẫu nhiên
$sql = "SELECT * FROM mc_questions ORDER BY RAND() LIMIT 20";
$stmt = $conn->query($sql);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📝 Đề thi thử - Môn Toán</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- MathJax -->
  <script>
    window.MathJax = {
      tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>

  <style>
    body { margin: 0; font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
    
    /* ===== Header ===== */
    .header {
      display: flex; justify-content: space-between; align-items: center;
      padding: 15px 30px; background: #2c3e50; color: white;
      border-bottom: 4px solid #3498db;
    }
    .logo { font-size: 24px; font-weight: bold; color: #f1c40f; }
    .exam-title { font-size: 20px; font-weight: bold; text-align: right; max-width: 70%; }

    /* ===== Layout chính ===== */
    .exam-container { display: flex; height: calc(100vh - 70px); }
    /* Cột trái */
    .left-col { flex: 3; padding: 20px; overflow-y: auto; background: #fff; border-right: 2px solid #ddd; }
    .question { margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa; }
    .question h3 { margin-top: 0; }
    .question img { max-width: 100%; margin: 10px 0; border-radius: 6px; border: 1px solid #ccc; }
    .choices { display: flex; flex-wrap: wrap; gap: 10px 20px; margin-top: 10px; }
    .choices label { display: flex; align-items: center; gap: 5px; white-space: nowrap; }

    /* Cột phải */
    .right-col { flex: 1; padding: 20px; background: #fdfdfd; display: flex; flex-direction: column; justify-content: space-between; }
    .answer-sheet { border: 1px solid #ccc; border-radius: 8px; padding: 10px; background: #fff; flex: 1; overflow-y: auto; }
    .answer-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; flex-wrap: wrap; }
    .answer-row span { width: 30px; font-weight: bold; }
    .answer-row label { margin-right: 4px; }
    .actions { margin-top: 15px; text-align: center; }
    .actions button { margin: 5px; padding: 10px 20px; font-size: 16px; border: none; border-radius: 6px; cursor: pointer; background: #3498db; color: #fff; transition: 0.3s; }
    .actions button:hover { background: #217dbb; }
  </style>
</head>
<body>
  <!-- Header -->
  <div class="header">
    <div class="logo">📘 Thầy Cường</div>
    <div class="exam-title">Đề thi thử tham khảo tốt nghiệp phổ thông 2026<br>Môn Toán</div>
  </div>

  <div class="exam-container">
    <!-- Cột trái: Câu hỏi -->
    <div class="left-col" id="leftCol">
      <?php foreach ($questions as $index => $q): ?>
        <div class="question" data-q="<?= $index+1 ?>" id="q<?= $index+1 ?>">
          <h3>Câu <?= $index+1 ?>: <?= $q['mc_question'] ?></h3>

          <?php if (!empty($q['mc_image_url'])): ?>
            <img src="<?= htmlspecialchars($q['mc_image_url']) ?>" alt="Hình minh họa">
          <?php endif; ?>

          <div class="choices">
            <?php foreach (['A','B','C','D'] as $opt): ?>
              <label>
                <input type="radio"
                       name="q<?= $index+1 ?>"
                       value="<?= $opt ?>"
                       onchange="syncAnswer(<?= $index+1 ?>,'<?= $opt ?>')">
                <?= $opt ?>.
                <?= $q['mc_answer'.($opt=='A'?1:($opt=='B'?2:($opt=='C'?3:4)))] ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Cột phải: Phiếu trả lời -->
    <div class="right-col">
      <div class="answer-sheet">
        <?php for ($i=1; $i<=count($questions); $i++): ?>
          <div class="answer-row" data-q="<?= $i ?>">
            <span><?= $i ?>.</span>
            <?php foreach (['A','B','C','D'] as $opt): ?>
              <label>
                <input type="radio"
                       name="ans<?= $i ?>"
                       value="<?= $opt ?>"
                       onchange="syncQuestion(<?= $i ?>,'<?= $opt ?>')"> <?= $opt ?>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endfor; ?>
      </div>
      <div class="actions">
        <button type="button" onclick="submitExam()">📤 Nộp bài</button>
        <button type="button" onclick="showAnswers()">👀 Xem đáp án</button>
      </div>
    </div>
  </div>

  <script>
    function syncAnswer(qIndex, opt) {
      const row = document.querySelector(`.answer-row[data-q="${qIndex}"]`);
      if (!row) return;
      const radio = row.querySelector(`input[type="radio"][value="${opt}"]`);
      if (radio) radio.checked = true;
    }

    function syncQuestion(qIndex, opt) {
      const qBlock = document.querySelector(`.question[data-q="${qIndex}"]`);
      if (!qBlock) return;
      qBlock.querySelectorAll('input[type="radio"]').forEach(r => r.checked = (r.value === opt));
      qBlock.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function submitExam() {
      const answers = {};
      document.querySelectorAll('.question').forEach(q => {
        const idx = q.getAttribute('data-q');
        const checked = q.querySelector('input[type="radio"]:checked');
        answers[`q${idx}`] = checked ? checked.value : null;
      });
      console.log('Bài làm:', answers);
      alert('📤 Đã thu bài (demo). Bạn có thể gửi lên server để chấm.');
    }

    function showAnswers() {
      alert('👀 Xem đáp án: cần server trả đáp án đúng để highlight.');
    }
  </script>
</body>
</html>
