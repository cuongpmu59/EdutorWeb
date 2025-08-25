<?php
// exam_form.php
require_once __DIR__ . '/../../includes/db_connection.php'; // kết nối $conn

// Lấy 20 câu hỏi ngẫu nhiên
$sql = "SELECT * FROM mc_questions ORDER BY RAND() LIMIT 20";
$stmt = $conn->query($sql);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>📝 Đề thi thử - Môn Toán</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- MathJax (render LaTeX) -->
  <script>
    window.MathJax = {
      tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js"></script>

  <style>
    :root {
      --accent: #3498db;
      --correct-border: #27ae60;
      --wrong-bg: #f8d7da;
    }

    body { margin: 0; font-family: Inter, "Segoe UI", Roboto, Arial, sans-serif; background: #f5f7fb; color: #222; }

    /* Header */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 28px;
      background: linear-gradient(90deg,#243b55 0%, #141e30 100%);
      color: #fff;
      border-bottom: 4px solid var(--accent);
    }
    .logo { display:flex; align-items:center; gap:12px; font-weight:700; font-size:20px; color:#ffd54a; }
    .logo .icon { font-size:28px; }
    .exam-title { text-align:right; font-size:16px; line-height:1.1; font-weight:600; max-width:70%; }

    /* Layout */
    .exam-container { display:flex; height: calc(100vh - 70px); gap: 0; }

    /* Left - questions */
    .left-col { flex: 3; padding: 18px; overflow-y: auto; background: #ffffff; border-right: 1px solid #e6e9ef; transition: opacity .3s ease; }
    .question { margin-bottom: 20px; padding: 14px; border-radius: 10px; background: #fbfdff; border: 1px solid #e6eef8; box-shadow: 0 1px 4px rgba(18,40,80,0.03); }
    .question h3 { margin: 0 0 8px 0; font-size:16px; font-weight:600; color:#0f2140; }
    .question .img-wrap { margin: 10px 0; text-align:center; }
    .question img { max-width:100%; height:auto; border-radius:6px; border:1px solid #dde8f5; }

    /* Choices: same row, wrap when too long */
    .choices { display:flex; flex-wrap:wrap; gap:12px 18px; margin-top:8px; }
    .choice { display:flex; align-items:flex-start; gap:8px; background: #fff; border-radius:8px; padding:8px 10px; border:1px solid #e8f0fb; min-width: 220px; max-width: calc(50% - 20px); box-sizing: border-box; }
    .choice input[type="radio"] { margin-top:3px; }
    .choice .text { white-space: normal; font-size:14px; color:#123; line-height:1.35; }

    /* Right - answer sheet */
    .right-col { flex: 1; padding: 18px; background: #fbfdff; display:flex; flex-direction:column; gap:12px; transition: opacity .3s ease; }
    .answer-sheet { border-radius:10px; border:1px solid #e6eef8; padding:12px; background:#fff; overflow-y:auto; flex:1; }
    .answer-sheet h3 { margin:6px 0 12px 0; text-align:center; color:#0f2140; font-size:18px; }
    .answer-row { display:flex; align-items:center; gap:8px; padding:6px 4px; border-radius:6px; }
    .answer-row span.num { width:34px; font-weight:700; color:#1b2b48; }
    .answer-row label { display:inline-flex; align-items:center; gap:6px; padding:4px 6px; border-radius:6px; cursor:pointer; }
    .answer-row input[type="radio"] { transform:scale(1); }

    /* Correct answer highlight: square border */
    .mark-correct { outline: 3px solid var(--correct-border); outline-offset: -4px; border-radius:6px; }

    /* Wrong selection highlight (the student's wrong choice) */
    .mark-wrong { background: var(--wrong-bg); border-radius:6px; }

    /* Buttons */
    .actions { display:flex; justify-content:center; gap:10px; margin-top:8px; }
    .btn {
      border: none; padding:10px 16px; border-radius:8px; cursor:pointer; font-weight:600; font-size:14px;
      color:#fff; background:var(--accent);
      transition: transform .08s ease, filter .08s ease;
    }
    .btn:active { transform: translateY(1px); }
    .btn[disabled] { background:#9aa6b2; cursor:not-allowed; }

    /* Disabled container styling (dim) */
    .dim { opacity: 0.42; pointer-events: none; filter: grayscale(.02); }

    /* small screens */
    @media (max-width: 900px) {
      .exam-container { flex-direction: column; height: auto; }
      .left-col { order:1; height:auto; }
      .right-col { order:2; height:auto; }
      .choice { max-width: 100%; min-width: auto; }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="logo"><span class="icon">📘</span><span>Thầy Cường</span></div>
    <div class="exam-title">Đề thi thử tham khảo tốt nghiệp phổ thông 2026<br><strong>Môn Toán</strong></div>
  </header>

  <div class="exam-container">
    <!-- Left: questions -->
    <div class="left-col" id="leftCol">
      <?php foreach ($questions as $index => $q): 
        $num = $index + 1;
        // ensure correct-answer normalized to A/B/C/D (if stored otherwise adapt)
        $correct = isset($q['mc_correct_answer']) ? $q['mc_correct_answer'] : '';
      ?>
        <div class="question" data-q="<?= $num ?>" data-correct="<?= htmlspecialchars($correct) ?>">
          <h3>Câu <?= $num ?>: <span class="q-text"><?= $q['mc_question'] ?></span></h3>

          <?php if (!empty($q['mc_image_url'])): ?>
            <div class="img-wrap">
              <img src="<?= htmlspecialchars($q['mc_image_url']) ?>" alt="Hình minh họa câu <?= $num ?>">
            </div>
          <?php endif; ?>

          <div class="choices">
            <?php
              $opts = ['A'=>'mc_answer1','B'=>'mc_answer2','C'=>'mc_answer3','D'=>'mc_answer4'];
              foreach ($opts as $label => $col):
                $text = isset($q[$col]) ? $q[$col] : '';
                // ID for label so we can mark it later: q{num}_{label}
                $labId = "q{$num}_{$label}";
            ?>
              <label class="choice" id="<?= $labId ?>">
                <input type="radio" name="q<?= $num ?>" value="<?= $label ?>" onchange="syncAnswer(<?= $num ?>,'<?= $label ?>')">
                <div class="text"><?= $label ?>. <span class="latex"><?= $text ?></span></div>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Right: answer sheet -->
    <div class="right-col">
      <div class="answer-sheet" id="answerSheet">
        <h3>📝 Phiếu trả lời</h3>
        <?php for ($i=1; $i<=count($questions); $i++): ?>
          <div class="answer-row" data-q="<?= $i ?>">
            <span class="num"><?= $i ?>.</span>
            <?php foreach (['A','B','C','D'] as $opt): 
              $aId = "a{$i}_{$opt}";
            ?>
              <label id="<?= $aId ?>">
                <input type="radio" name="ans<?= $i ?>" value="<?= $opt ?>" onchange="syncQuestion(<?= $i ?>,'<?= $opt ?>')"> <?= $opt ?>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endfor; ?>
      </div>

      <div class="actions">
        <button class="btn" id="btnSubmit" onclick="handleSubmit()">📤 Nộp bài</button>
        <button class="btn" id="btnShow" onclick="handleShowAnswers()" disabled>👀 Xem đáp án</button>
      </div>
    </div>
  </div>

  <script>
  window.addEventListener('load', () => {
    if (window.MathJax) {
      MathJax.typesetPromise && MathJax.typesetPromise();
    }
    document.getElementById('btnShow').disabled = true;
  });

  function syncAnswer(qIndex, opt) {
    const rightRadio = document.querySelector(`input[name="ans${qIndex}"][value="${opt}"]`);
    if (rightRadio) rightRadio.checked = true;
  }

  function syncQuestion(qIndex, opt) {
    const leftRadio = document.querySelector(`input[name="q${qIndex}"][value="${opt}"]`);
    if (leftRadio) leftRadio.checked = true;
    const qBlock = document.querySelector(`.question[data-q="${qIndex}"]`);
    if (qBlock) qBlock.scrollIntoView({ behavior:'smooth', block:'center' });
  }

  // Sau khi nộp bài
  function handleSubmit() {
    // làm mờ câu hỏi + phiếu trả lời
    document.getElementById('leftCol').classList.add('dim');
    document.querySelector('.right-col').classList.add('dim');
    // bật lại nút xem đáp án
    document.getElementById('btnShow').disabled = false;
    document.getElementById('btnSubmit').disabled = true;
    alert('📤 Bạn đã nộp bài. Có thể nhấn "Xem đáp án" để kiểm tra.');
  }

  // Khi xem đáp án
  function handleShowAnswers() {
    // bỏ mờ để hiển thị rõ
    document.getElementById('leftCol').classList.remove('dim');
    document.querySelector('.right-col').classList.remove('dim');

    // reset đánh dấu cũ (nếu người dùng bấm lại nhiều lần)
    document.querySelectorAll('.mark-correct, .mark-wrong').forEach(el => {
      el.classList.remove('mark-correct','mark-wrong');
    });

    // duyệt từng câu hỏi bên trái
    document.querySelectorAll('.question').forEach(q => {
      const qIdx = q.getAttribute('data-q');
      const correct = q.getAttribute('data-correct') || '';

      if (correct) {
        // đánh dấu đáp án đúng
        const leftLabel = document.getElementById(`q${qIdx}_${correct}`);
        if (leftLabel) leftLabel.classList.add('mark-correct');
      }

      // kiểm tra lựa chọn của user
      const chosenLeft = q.querySelector('input[type="radio"]:checked');
      if (chosenLeft) {
        const chosenVal = chosenLeft.value;
        if (correct && chosenVal !== correct) {
          const wrongLeft = document.getElementById(`q${qIdx}_${chosenVal}`);
          if (wrongLeft) wrongLeft.classList.add('mark-wrong');
        }
      }
    });

    // duyệt phiếu trả lời bên phải
    document.querySelectorAll('.answer-row').forEach(r => {
      const qIdx = r.getAttribute('data-q');
      const qBlock = document.querySelector(`.question[data-q="${qIdx}"]`);
      const correct = qBlock ? qBlock.getAttribute('data-correct') : '';

      if (correct) {
        const rightLabel = document.getElementById(`a${qIdx}_${correct}`);
        if (rightLabel) rightLabel.classList.add('mark-correct');
      }

      const chosenRight = r.querySelector('input[type="radio"]:checked');
      if (chosenRight) {
        const chosenVal = chosenRight.value;
        if (correct && chosenVal !== correct) {
          const wrongRight = document.getElementById(`a${qIdx}_${chosenVal}`);
          if (wrongRight) wrongRight.classList.add('mark-wrong');
        }
      }
    });

    // render lại LaTeX (nếu có)
    if (window.MathJax && MathJax.typesetPromise) MathJax.typesetPromise();
  }
</script>
</body>
</html>
