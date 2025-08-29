<?php
// sa_exam_form.php
require_once __DIR__ . '/../../includes/env/db_connection.php';

// Lấy 20 câu hỏi ngẫu nhiên
$sql = "
    SELECT * 
    FROM sa_questions 
    WHERE sa_id IN (
        SELECT sa_id FROM (
            SELECT sa_id,
                   ROW_NUMBER() OVER (PARTITION BY sa_topic ORDER BY RAND()) as rn
            FROM sa_questions
        ) t
        WHERE rn <= 20
    )
    ORDER BY RAND()
    LIMIT 20
";
$stmt = $conn->query($sql);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đề thi thử 2026 - Môn SA</title>
<link rel="stylesheet" href="../../css/main/exam_form.css">

<!-- MathJax -->
<script>
window.MathJax = {
  tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
  svg: { fontCache: 'global' }
};
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>
</head>
<body>
<header>
  <div class="logo-left">
    <img src="../../pages/image/logo_cuong.jpg" alt="Logo">
  </div>
  <div class="header-center">
    <div class="exam-title">Đề thi thử tham khảo tốt nghiệp phổ thông quốc gia</div>
    <div class="subject">Môn thi: SA</div>
  </div>
  <div class="header-right">
    <div class="time">⏰ Thời gian làm bài: 20 phút</div>
    <div class="progress-container">
      <div class="progress-bar" id="progressBar"></div>
    </div>
    <div class="countdown" id="countdown">20:00</div>
  </div>
</header>

<div class="container">
  <!-- Cột trái: Câu hỏi -->
  <div class="left-col" id="leftCol">
    <?php foreach ($questions as $index => $q): ?>
      <fieldset class="topic-block">
        <legend><strong><?= htmlspecialchars($q['sa_topic']) ?></strong></legend>
        <div class="question" data-qid="<?= $q['sa_id'] ?>">
          <h3>Câu <?= $index+1 ?>:</h3>
          <div class="qtext"><?= $q['sa_question'] ?></div>

          <?php if (!empty($q['sa_image_url'])): ?>
            <div class="qimage">
              <img src="<?= htmlspecialchars($q['sa_image_url']) ?>" alt="Minh họa câu hỏi <?= $index+1 ?>">
            </div>
          <?php endif; ?>

          <div class="answers layout-sa">
            <input type="text" id="input<?= $index ?>" 
                   placeholder="Nhập đáp án..." 
                   oninput="syncAnswer(<?= $index ?>, this.value)">
          </div>

          <input type="hidden" id="correct<?= $index ?>" value="<?= htmlspecialchars($q['sa_correct_answer']) ?>">
        </div>
      </fieldset>
    <?php endforeach; ?>
  </div>

  <!-- Cột phải: Phiếu trả lời -->
  <div class="right-col">
    <div class="answer-sheet" id="answerSheet">
      <h3>Phiếu trả lời</h3>
      <?php foreach ($questions as $index => $q): ?>
        <fieldset class="answer-row">
          <legend>Câu <?= $index+1 ?></legend>
          <input type="text" id="sheet<?= $index ?>" 
                 placeholder="Nhập đáp án..." 
                 oninput="syncQuestion(<?= $index ?>, this.value)">
        </fieldset>
      <?php endforeach; ?>
    </div>

    <div class="actions">
      <button type="button" id="btnSubmit" onclick="handleSubmit()">Nộp bài</button>
      <button type="button" id="btnShow" onclick="handleShowAnswers()" disabled>Xem đáp án</button>
      <button type="button" id="btnReset" onclick="handleReset()">Reset</button>
    </div>
    <div class="score-box" id="scoreBox" style="display:none;"></div>
  </div>
</div>

<audio id="tickSound" src="../../pages/sound/tick_sound.mp3" preload="auto"></audio>
<audio id="bellSound" src="../../pages/sound/bell_sound.mp3" preload="auto"></audio>

<script>
// Đồng bộ giữa cột câu hỏi và phiếu trả lời
function syncAnswer(idx, val){
  const sheet = document.getElementById(`sheet${idx}`);
  if(sheet) sheet.value = val;
}
function syncQuestion(idx, val){
  const input = document.getElementById(`input${idx}`);
  if(input) input.value = val;
}

// Chấm điểm
function handleSubmit(){
  const total = <?= count($questions) ?>;
  let score = 0;
  for(let i=0;i<total;i++){
    const answer = document.getElementById(`input${i}`).value.trim();
    const correct = document.getElementById(`correct${i}`).value.trim();
    if(answer === correct) score++;
  }
  const percent = (score / total * 100).toFixed(2);
  const box = document.getElementById('scoreBox');
  box.style.display = 'block';
  box.innerHTML = `Bạn đạt: ${score} / ${total} (${percent}%)`;
}

// Xem đáp án
function handleShowAnswers(){
  const total = <?= count($questions) ?>;
  for(let i=0;i<total;i++){
    document.getElementById(`input${i}`).value = document.getElementById(`correct${i}`).value;
    document.getElementById(`sheet${i}`).value = document.getElementById(`correct${i}`).value;
  }
  document.getElementById('btnShow').disabled = true;
}

// Reset
function handleReset(){
  const total = <?= count($questions) ?>;
  for(let i=0;i<total;i++){
    document.getElementById(`input${i}`).value = '';
    document.getElementById(`sheet${i}`).value = '';
  }
  document.getElementById('scoreBox').style.display = 'none';
  document.getElementById('btnShow').disabled = false;
}
</script>

</body>
</html>
