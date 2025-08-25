<?php
// exam_form.php
require_once __DIR__ . '/../../includes/db_connection.php'; // Kết nối PDO

// ===== Lấy 20 câu hỏi ngẫu nhiên, đủ topic =====
$sql = "SELECT * FROM mc_questions ORDER BY RAND() LIMIT 20";
$stmt = $pdo->query($sql);
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
    .exam-container {
      display: flex;
      height: 100vh;
    }
    /* Cột trái */
    .left-col {
      flex: 3;
      padding: 20px;
      overflow-y: auto;
      background: #fff;
      border-right: 2px solid #ddd;
    }
    .question {
      margin-bottom: 25px;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background: #fafafa;
    }
    .question h3 {
      margin-top: 0;
    }
    .choices label {
      display: block;
      margin: 5px 0;
    }
    /* Cột phải */
    .right-col {
      flex: 1;
      padding: 20px;
      background: #fdfdfd;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .answer-sheet {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 10px;
      background: #fff;
      flex: 1;
      overflow-y: auto;
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
    .answer-row label {
      margin-right: 10px;
    }
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
    .actions button:hover {
      background: #217dbb;
    }
  </style>
</head>
<body>
  <div class="exam-container">
    <!-- Cột trái: Câu hỏi -->
    <div class="left-col">
      <?php foreach ($questions as $index => $q): ?>
        <div class="question" data-q="<?= $index+1 ?>">
          <h3>Câu <?= $index+1 ?>: <?= htmlspecialchars($q['mc_question']) ?></h3>
          <div class="choices">
            <?php foreach (['A','B','C','D'] as $opt): ?>
              <label>
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
<div class="right-col">
  <div class="answer-sheet">
    <?php for ($i=1; $i<=count($questions); $i++): ?>
      <div class="answer-row" data-q="<?= $i ?>">
        <span><?= $i ?>.</span>
        <?php foreach (['A','B','C','D'] as $opt): ?>
          <label>
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
    <button onclick="showAnswers()">👀 Xem đáp án</button>
  </div>
</div>
