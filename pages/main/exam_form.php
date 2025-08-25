<?php
// exam_form.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$questions = [];

try {
    require_once __DIR__ . '/../../includes/db_connection.php'; // phải tạo $pdo (PDO)
    if (!isset($pdo)) {
        throw new Exception('Kết nối PDO ($pdo) chưa được khởi tạo trong db_connection.php');
    }

    // Lấy 20 câu hỏi ngẫu nhiên
    $sql = "SELECT mc_id, mc_topic, mc_question, mc_answer1, mc_answer2, mc_answer3, mc_answer4
            FROM mc_questions
            ORDER BY RAND() LIMIT 20";
    $stmt = $pdo->query($sql);
    $questions = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

    if (!$questions || count($questions) === 0) {
        throw new Exception('Bảng mc_questions không có dữ liệu hoặc truy vấn trả về rỗng.');
    }
} catch (Throwable $e) {
    // Fallback dữ liệu mẫu để bạn vẫn test được UI/JS
    $msg = $e->getMessage();
    $questions = [];
    for ($i = 1; $i <= 20; $i++) {
        $questions[] = [
            'mc_id' => $i,
            'mc_topic' => 'Demo',
            'mc_question' => "Câu hỏi mẫu số {$i} (DB lỗi: {$msg})",
            'mc_answer1' => 'Đáp án A',
            'mc_answer2' => 'Đáp án B',
            'mc_answer3' => 'Đáp án C',
            'mc_answer4' => 'Đáp án D',
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📝 Bài thi trắc nghiệm</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
    .exam-container { display: flex; height: 100vh; }
    /* Cột trái */
    .left-col { flex: 3; padding: 20px; overflow-y: auto; background: #fff; border-right: 2px solid #ddd; }
    .question { margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa; }
    .question h3 { margin-top: 0; }
    .choices label { display: block; margin: 5px 0; }
    /* Cột phải */
    .right-col { flex: 1; padding: 20px; background: #fdfdfd; display: flex; flex-direction: column; justify-content: space-between; }
    .answer-sheet { border: 1px solid #ccc; border-radius: 8px; padding: 10px; background: #fff; flex: 1; overflow-y: auto; }
    .answer-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
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
        <div class="question" data-q="<?php echo $index+1; ?>" id="q<?php echo $index+1; ?>">
          <h3>
            Câu <?php echo $index+1; ?>:
            <?php echo htmlspecialchars($q['mc_question'] ?? ''); ?>
          </h3>
          <div class="choices">
            <?php foreach (['A','B','C','D'] as $opt): ?>
              <?php
                $col = [
                  'A' => 'mc_answer1',
                  'B' => 'mc_answer2',
                  'C' => 'mc_answer3',
                  'D' => 'mc_answer4'
                ][$opt];
              ?>
              <label>
                <input type="radio"
                       name="q<?php echo $index+1; ?>"
                       value="<?php echo $opt; ?>"
                       onchange="syncAnswer(<?php echo $index+1; ?>,'<?php echo $opt; ?>')">
                <?php echo $opt; ?>.
                <?php echo htmlspecialchars($q[$col] ?? ''); ?>
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
          <div class="answer-row" data-q="<?php echo $i; ?>">
            <span><?php echo $i; ?>.</span>
            <?php foreach (['A','B','C','D'] as $opt): ?>
              <label>
                <input type="radio"
                       name="ans<?php echo $i; ?>"
                       value="<?php echo $opt; ?>"
                       onchange="syncQuestion(<?php echo $i; ?>,'<?php echo $opt; ?>')"> <?php echo $opt; ?>
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

    // Thu bài (demo): gom đáp án người dùng
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
