<?php
// Kết nối CSDL
require_once __DIR__ . '/../../includes/db_connection.php';

// Lấy 20 câu hỏi ngẫu nhiên, đảm bảo đủ topic
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
  <div class="logo">
    <img src="../../pages/image/logo_cuong.jpg" alt="Logo" style="height:50px; border-radius:6px; vertical-align:middle;">
  </div>
  <div class="title">Đề thi thử tham khảo tốt nghiệp phổ thông 2026 - Môn Toán</div>
</header>

<div class="container">
  <!-- Cột trái: câu hỏi -->
  <div class="left-col" id="leftCol">
    <?php foreach ($questions as $index => $q): ?>
      <div class="question" data-qid="<?= $q['mc_id'] ?>">
        <h3>Câu <?= $index+1 ?> (<?= htmlspecialchars($q['mc_topic']) ?>):</h3>
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
    <?php endforeach; ?>
  </div>

  <!-- Cột phải: phiếu trả lời -->
  <div class="right-col">
    <div class="answer-sheet" id="answerSheet">
      <h3>Phiếu trả lời</h3>
      <?php foreach ($questions as $index => $q): ?>
        <div class="answer-row">
          <span><?= $index+1 ?>.</span>
          <?php foreach (['A','B','C','D'] as $opt): ?>
            <label style="margin-right:5px;">
              <input type="radio" 
                     name="s<?= $index ?>" 
                     value="<?= $opt ?>" 
                     onchange="syncQuestion(<?= $index ?>,'<?= $opt ?>')">
              <?= $opt ?>
            </label>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="actions">
      <button id="btnSubmit" onclick="handleSubmit()">Nộp bài</button>
      <button id="btnShow" onclick="handleShowAnswers()" disabled>Xem đáp án</button>
    </div>
  </div>
</div>

<script>
// Đồng bộ khi chọn ở câu hỏi
function syncAnswer(idx,opt){
  document.querySelector(`input[name="s${idx}"][value="${opt}"]`).checked = true;
}
// Đồng bộ khi chọn ở phiếu trả lời
function syncQuestion(idx,opt){
  document.querySelector(`input[name="q${idx}"][value="${opt}"]`).checked = true;
}

function handleSubmit(){
  document.getElementById('leftCol').classList.add('dim');
  document.getElementById('answerSheet').classList.add('dim');
  document.getElementById('btnShow').disabled = false;
  document.getElementById('btnSubmit').disabled = true;
  alert("📤 Bạn đã nộp bài. Hãy nhấn 'Xem đáp án' để kiểm tra.");
}

function handleShowAnswers(){
  document.getElementById('leftCol').classList.remove('dim');
  document.getElementById('answerSheet').classList.remove('dim');
  // highlight đáp án đúng
  document.querySelectorAll('.question').forEach((qDiv,idx)=>{
    let correct = document.getElementById('correct'+idx).value;
    let radios = qDiv.querySelectorAll(`input[type=radio]`);
    radios.forEach(r=>{
      if(r.value===correct){
        r.parentElement.classList.add('correct-answer');
      }
    });
    // Phiếu trả lời cũng highlight
    let sheetRadios = document.querySelectorAll(`input[name="s${idx}"]`);
    sheetRadios.forEach(r=>{
      if(r.value===correct){
        r.parentElement.classList.add('correct-answer');
      }
    });
  });
  MathJax.typesetPromise();
}

// Render lại LaTeX khi load trang
document.addEventListener("DOMContentLoaded", () => {
  MathJax.typesetPromise();
});
</script>
</body>
</html>
