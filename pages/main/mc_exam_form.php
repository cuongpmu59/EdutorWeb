<?php
// exam_form.php
require_once __DIR__ . '/../../includes/env/db_connection.php';

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

  <!-- Tiêu đề giữa -->
  <div class="header-center">
    <div class="exam-title">Đề thi thử tham khảo tốt nghiệp phổ thông quốc gia</div>
    <div class="subject">Môn thi: Toán</div>
  </div>

  <!-- Thời gian bên phải -->
  <div class="header-right">
    <div class="time">⏰ Thời gian làm bài: 20 phút</div>
    <div class="progress-container">
      <div class="progress-bar" id="progressBar"></div>
    </div>
    <div class="countdown" id="countdown">20:00</div>
  </div>
</header>

<!-- Container chung -->
<div class="container">
  <!-- Cột trái: Câu hỏi -->
  <div class="left-col" id="leftCol">
    <?php foreach ($questions as $index => $q): ?>
      <fieldset class="topic-block">
        <legend><strong><?= htmlspecialchars($q['mc_topic']) ?></strong></legend>
        <div class="question" data-qid="<?= $q['mc_id'] ?>">
          <h3>Câu <?= $index+1 ?>:</h3>
          <div class="qtext"><?= $q['mc_question'] ?></div>

          <?php if (!empty($q['mc_image_url'])): ?>
            <div class="qimage">
              <img src="<?= htmlspecialchars($q['mc_image_url']) ?>" alt="Minh họa câu hỏi <?= $index+1 ?>">
            </div>
          <?php endif; ?>

          <?php 
            // --- Phân loại độ dài đáp án ---
            $lengths = [
              strlen(strip_tags($q['mc_answer1'])),
              strlen(strip_tags($q['mc_answer2'])),
              strlen(strip_tags($q['mc_answer3'])),
              strlen(strip_tags($q['mc_answer4']))
            ];
            $maxLen = max($lengths);

            if($maxLen <= 30){
              $layoutClass = "layout-1"; // Ngắn: 1 dòng 4 cột
            } elseif($maxLen <= 80){
              $layoutClass = "layout-2"; // Trung bình: 2 dòng × 2 cột
            } else {
              $layoutClass = "layout-3"; // Dài: 4 dòng (đổi từ layout-4 sang layout-3)
            }
          ?>

          <div class="answers <?= $layoutClass ?>">
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

  <!-- Cột phải: Phiếu trả lời -->
  <div class="right-col">
    <div class="answer-sheet" id="answerSheet">
      <h3>Phiếu trả lời</h3>
      <?php foreach ($questions as $index => $q): ?>
        <fieldset class="answer-row">
          <legend>Câu <?= $index+1 ?></legend>
          <?php foreach (['A','B','C','D'] as $opt): ?>
            <label>
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

    <!-- Nút thao tác -->
    <div class="actions">
      <button type="button" id="btnSubmit" onclick="handleSubmit()">Nộp bài</button>
      <button type="button" id="btnShow" onclick="handleShowAnswers()" disabled>Xem đáp án</button>
      <button type="button" id="btnReset" onclick="handleReset()">Reset</button>
    </div>
    <div class="score-box" id="scoreBox" style="display:none;"></div>
  </div>
</div>

<!-- Âm thanh -->
<audio id="tickSound" src="../../pages/sound/tick_sound.mp3" preload="auto"></audio>
<audio id="bellSound" src="../../pages/sound/bell_sound.mp3" preload="auto"></audio>
<script src="../../js/main/exam_form.js"></script>

</body>
</html>
