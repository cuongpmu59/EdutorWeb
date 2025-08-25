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
  <title>📝 Bài thi trắc nghiệm</title>
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
    .exam-container { display: flex; height: 100vh; }
    /* Cột trái */
    .left-col { flex: 3; padding: 20px; overflow-y: auto; background: #fff; border-right: 2px solid #ddd; }
    .question { margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa; }
    .question h3 { margin-top: 0; }
    .question img { max-width: 100%; margin: 10px 0; display: block; }
    .choices { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 10px; }
    .choices label { display: flex; align-items: center; gap: 5px; white-space: normal; }

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
  <div class="exam-container">
    <!-- Cột trái: Câu hỏi -->
    <div class="left-col" id="leftCol">
      <?php foreach ($questions as $index => $q): ?>
        <div class="question" data-q="<?= $index+1 ?>" id="q<?= $index+1 ?>">
          <h3>Câu <?= $index+1 ?>: <span class="mathjax"><?= $q['mc_question'] ?></span></h3>
          
          <?php if (!empty($q['mc_image_url'])): ?>
            <img src="<?= htmlspecialchars($q['mc_image_url']) ?>" alt="Hình câu hỏi">
          <?php endif; ?>

          <div class="choices">
            <?php foreach (['A','B','C','D'] as $opt): ?>
              <?php 
                $col = ($opt=='A'?1:($opt=='B'?2:($opt=='C'?3:4)));
                $ans = $q['mc_answer'.$col];
              ?>
              <label>
                <input type="radio"
                       name="q<?= $index+1 ?>"
                       value="<?= $opt ?>"
                       onchange="syncAnswer(<?= $index+1 ?>,'<?= $opt ?>')">
                <span class="mathjax"><?= $opt ?>. <?= $ans ?></span>
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
    // Đồng bộ: chọn ở cột trái -> tick ở phiếu trả lời
    function syncAnswer(qIndex, opt) {
      const row = document.querySelector(`.answer-row[data-q="${qIndex}"]`);
      if (!row) return;
      const radio = row.querySelector(`input[type="radio"][value="${opt}"]`);
      if (radio) radio.checked = true;
    }

    // Đồng bộ: chọn ở phiếu trả lời -> tick ở cột trái và cuộn tới câu hỏi
    function syncQuestion(qIndex, opt) {
      const qBlock = document.querySelector(`.question[data-q="${qIndex}"]`);
      if (!qBlock) return;
      qBlock.querySelectorAll('input[type="radio"]').forEach(r => r.checked = (r.value === opt));
      qBlock.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Demo nộp bài
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

    // Demo xem đáp án
    function showAnswers() {
      alert('👀 Xem đáp án: cần server trả đáp án đúng để highlight.');
    }
  </script>
</body>
</html>
