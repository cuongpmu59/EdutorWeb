<?php
// exam_form.php
require_once __DIR__ . '/../../includes/db_connection.php';

// Lấy 20 câu hỏi ngẫu nhiên
$sql = "
    SELECT * 
    FROM mc_questions 
    WHERE mc_id IN (
        SELECT mc_id FROM (
            SELECT mc_id,
                   ROW_NUMBER() OVER (PARTITION BY mc_topic ORDER BY RAND()) as rn
            FROM mc_questions
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
<title>Đề thi thử 2026 - Môn Toán</title>
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
  <!-- Logo bên trái -->
  <div class="logo-left">
    <img src="../../pages/image/logo_cuong.jpg" alt="Logo">
  </div>

  <!-- Giữa: Tiêu đề -->
  <div class="header-center">
    <div class="exam-title">Đề thi thử tham khảo tốt nghiệp phổ thông quốc gia</div>
    <div class="subject">Môn thi: Toán</div>
  </div>

  <!-- Bên phải: Thời gian + progress -->
  <div class="header-right">
    <div class="time">⏰ Thời gian làm bài: 20 phút</div>
    <div class="progress-container">
      <div class="progress-bar" id="progressBar"></div>
    </div>
    <div class="countdown" id="countdown">20:00</div>
  </div>
</header>

<div class="container">
  <!-- Cột trái: câu hỏi -->
  <div class="left-col" id="leftCol">
    <?php foreach ($questions as $index => $q): ?>
      <fieldset class="topic-block">
        <legend><strong><?= htmlspecialchars($q['mc_topic']) ?></strong></legend>
        <div class="question" data-qid="<?= $q['mc_id'] ?>">
          <h3>Câu <?= $index+1 ?>:</h3>
          <div class="qtext"><?= $q['mc_question'] ?></div>
          <div class="answers">
            <?php foreach (['A','B','C','D'] as $opt): ?>
              <label>
                <input type="radio" 
                       name="q<?= $index ?>" 
                       value="<?= $opt ?>" 
                       onchange="syncAnswer(<?= $index ?>,'<?= $opt ?>')">
                <?= $opt ?>. <?= $q['mc_answer'. (ord($opt)-64)] ?>
              </label>
            <?php endforeach; ?>
          </div>
          <input type="hidden" id="correct<?= $index ?>" value="<?= $q['mc_correct_answer'] ?>">
        </div>
      </fieldset>
    <?php endforeach; ?>
  </div>

  <!-- Cột phải: phiếu trả lời -->
  <div class="right-col">
    <div class="answer-sheet" id="answerSheet">
      <h3>Phiếu trả lời</h3>
      <?php foreach ($questions as $index => $q): ?>
        <fieldset class="answer-row">
          <legend>Câu <?= $index+1 ?></legend>
          <?php foreach (['A','B','C','D'] as $opt): ?>
            <label style="margin-right:5px;">
              <input type="radio" 
                     name="s<?= $index ?>" 
                     value="<?= $opt ?>" 
                     onchange="syncQuestion(<?= $index ?>,'<?= $opt ?>')">
              <?= $opt ?>
            </label>
          <?php endforeach; ?>
        </fieldset>
      <?php endforeach; ?>
    </div>

    <div class="actions">
      <button type="button" id="btnSubmit" onclick="handleSubmit()">Nộp bài</button>
      <button type="button" id="btnShow" onclick="handleShowAnswers()" disabled>Xem đáp án</button>
      <button type="button" id="btnReset" onclick="handleReset()">Reset</button>
    </div>
  </div>
</div>

<!-- Âm thanh -->
<audio id="tickSound" src="../../assets/sound/tick.mp3" preload="auto"></audio>
<audio id="bellSound" src="../../assets/sound/bell.mp3" preload="auto"></audio>

<script>
// === Đồng bộ phiếu trả lời ===
function syncAnswer(idx,opt){
  const r = document.querySelector(`input[name="s${idx}"][value="${opt}"]`);
  if (r) r.checked = true;
}
function syncQuestion(idx,opt){
  const r = document.querySelector(`input[name="q${idx}"][value="${opt}"]`);
  if (r) r.checked = true;
}

// === Timer + Progress bar ===
let duration = 20 * 60; // 20 phút (giây)
let remaining = duration;
let timer;

function formatTime(sec){
  const m = Math.floor(sec/60).toString().padStart(2,'0');
  const s = (sec%60).toString().padStart(2,'0');
  return `${m}:${s}`;
}

function startTimer() {
  clearInterval(timer);
  remaining = duration;
  document.getElementById('countdown').textContent = formatTime(remaining);

  const tickAudio = document.getElementById('tickSound');
  const bellAudio = document.getElementById('bellSound');

  timer = setInterval(()=>{
    remaining--;
    let percent = Math.max(0, Math.round(((duration-remaining)/duration)*100));
    document.getElementById('progressBar').style.width = percent + "%";
    document.getElementById('countdown').textContent = formatTime(remaining);

    if(remaining <= 60 && remaining > 0){
      tickAudio.currentTime = 0;
      tickAudio.play().catch(()=>{});
    }

    if(remaining <= 0){
      clearInterval(timer);
      bellAudio.currentTime = 0;
      bellAudio.play().catch(()=>{});
      handleSubmit(true);
      alert("⏰ Hết giờ! Hệ thống đã tự động nộp bài.");
    }
  },1000);
}

// === Nút xử lý ===
function handleSubmit(auto=false){
  document.getElementById('leftCol').classList.add('dim');
  document.getElementById('answerSheet').classList.add('dim');
  document.getElementById('btnShow').disabled = false;
  document.getElementById('btnSubmit').disabled = true;
  if(!auto){
    alert("📤 Bạn đã nộp bài thành công!");
  }
}

function handleShowAnswers(){
  document.getElementById('leftCol').classList.remove('dim');
  document.getElementById('answerSheet').classList.remove('dim');
  document.querySelectorAll('.question').forEach((qDiv,idx)=>{
    let correct = document.getElementById('correct'+idx).value;
    let radios = qDiv.querySelectorAll('input[type=radio]');
    radios.forEach(r=>{
      if(r.value===correct){
        r.parentElement.classList.add('correct-answer');
      }
    });
    let sheetRadios = document.querySelectorAll(`input[name="s${idx}"]`);
    sheetRadios.forEach(r=>{
      if(r.value===correct){
        r.parentElement.classList.add('correct-answer');
      }
    });
  });
  MathJax.typesetPromise();
}

function handleReset(){
  location.reload();
}

document.addEventListener("DOMContentLoaded", () => {
  MathJax.typesetPromise();
  startTimer();
});
</script>
</body>
</html>
