<?php
// exam_form.php
require_once __DIR__ . '/../../includes/db_connection.php';

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
<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    color: #333;
  }
  /* Header */
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background: #2c3e50;
    color: #fff;
  }
  .logo {
    font-size: 22px;
    font-weight: bold;
  }
  .exam-title {
    font-size: 18px;
    font-weight: bold;
  }
  /* Layout */
  .exam-container {
    display: flex;
    height: calc(100vh - 70px);
  }
  .left-col {
    flex: 3;
    padding: 20px;
    overflow-y: auto;
    background: #fff;
    border-right: 2px solid #ddd;
    transition: opacity 0.3s;
  }
  .question {
    margin-bottom: 25px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #fafafa;
  }
  .question h3 { margin-top: 0; }
  .choices label { display: block; margin: 5px 0; }

  .right-col {
    flex: 1;
    padding: 20px;
    background: #fdfdfd;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: opacity 0.3s;
  }
  .answer-sheet {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 10px;
    background: #fff;
    flex: 1;
    overflow-y: auto;
  }
  .answer-sheet h3 {
    text-align: center;
    margin: 10px 0 15px;
    font-size: 18px;
    color: #2c3e50;
  }
  .answer-row {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
  }
  .answer-row span {
    width: 30px;
    font-weight: bold;
  }
  .answer-row label { margin-right: 10px; position: relative; padding-left: 2px; }

  /* Đánh dấu đáp án đúng */
  .correct-answer {
    border: 2px solid #27ae60;
    border-radius: 4px;
    padding: 2px 4px;
  }

  /* Action buttons */
  .actions {
    margin-top: 15px;
    text-align: center;
  }
  .actions button {
    margin: 5px;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    background: #3498db;
    color: #fff;
    transition: 0.3s;
  }
  .actions button:disabled {
    background: #95a5a6;
    cursor: not-allowed;
  }
  .actions button:hover:not(:disabled) {
    background: #217dbb;
  }

  /* Làm mờ */
  .disabled {
    opacity: 0.5;
    pointer-events: none;
  }
</style>
</head>
<body>
  <!-- Header -->
  <div class="header">
    <div class="logo">📘 Thầy Cường</div>
    <div class="exam-title">Đề thi thử tham khảo tốt nghiệp phổ thông 2026 - Môn Toán</div>
  </div>

  <div class="exam-container">
    <!-- Cột trái: Câu hỏi -->
    <div class="left-col" id="questions">
      <?php foreach ($questions as $index => $q): ?>
        <div class="question" data-q="<?= $index+1 ?>" data-correct="<?= $q['mc_correct_answer'] ?>">
          <h3>Câu <?= $index+1 ?>: <?= htmlspecialchars($q['mc_question']) ?></h3>
          <div class="choices">
            <?php foreach (['A','B','C','D'] as $opt): ?>
              <label id="q<?= $index+1 ?>_<?= $opt ?>">
                <input type="radio" name="q<?= $index+1 ?>" value="<?= $opt ?>"
                       onchange="syncAnswer(<?= $index+1 ?>,'<?= $opt ?>')">
                <?= $opt ?>. <?= htmlspecialchars($q['mc_answer'.($opt=='A'?1:($opt=='B'?2:($opt=='C'?3:4)))]) ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Cột phải: Phiếu trả lời -->
    <div class="right-col" id="answers">
      <div class="answer-sheet">
        <h3>📝 Phiếu trả lời</h3>
        <?php for ($i=1; $i<=count($questions); $i++): ?>
          <div class="answer-row" data-q="<?= $i ?>">
            <span><?= $i ?>.</span>
            <?php foreach (['A','B','C','D'] as $opt): ?>
              <label id="a<?= $i ?>_<?= $opt ?>">
                <input type="radio" name="ans<?= $i ?>" value="<?= $opt ?>"
                       onchange="syncQuestion(<?= $i ?>,'<?= $opt ?>')">
                <?= $opt ?>
              </label>
            <?php endforeach; ?>
          </div>
        <?php endfor; ?>
      </div>
      <div class="actions">
        <button onclick="submitExam()">📤 Nộp bài</button>
        <button id="btnShowAns" onclick="showAnswers()" disabled>👀 Xem đáp án</button>
      </div>
    </div>
  </div>

<script>
function syncAnswer(q,opt){
  document.querySelector(`input[name="ans${q}"][value="${opt}"]`).checked = true;
}
function syncQuestion(q,opt){
  document.querySelector(`input[name="q${q}"][value="${opt}"]`).checked = true;
}

function submitExam(){
  document.getElementById('questions').classList.add('disabled');
  document.getElementById('answers').classList.add('disabled');
  document.getElementById('btnShowAns').disabled = false;
}

function showAnswers(){
  // Hiển thị lại
  document.getElementById('questions').classList.remove('disabled');
  document.getElementById('answers').classList.remove('disabled');

  // Đánh dấu đáp án đúng
  document.querySelectorAll('.question').forEach(q=>{
    let correct = q.dataset.correct;
    if(correct){
      let label = q.querySelector(`#q${q.dataset.q}_${correct}`);
      if(label) label.classList.add('correct-answer');
    }
  });
  document.querySelectorAll('.answer-row').forEach(r=>{
    let q = r.dataset.q;
    let correct = document.querySelector(`.question[data-q="${q}"]`).dataset.correct;
    if(correct){
      let label = r.querySelector(`#a${q}_${correct}`);
      if(label) label.classList.add('correct-answer');
    }
  });
}
</script>
</body>
</html>
