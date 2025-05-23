<?php
$host = "sql210.infinityfree.com"; 
$username = "if0_39047715";
$password = "Kimdung16091961";
$dbname = "if0_39047715_questionbank";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "SELECT * FROM questions ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách câu hỏi</title>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 30px;
      max-width: 900px;
    }
    .question {
      border-bottom: 1px solid #ccc;
      margin-bottom: 20px;
      padding-bottom: 10px;
    }
    img {
      max-width: 200px;
      display: block;
      margin-top: 10px;
    }
    .correct {
      color: green;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <h1>Danh sách câu hỏi trắc nghiệm</h1>

  <?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="question">
        <div>Câu hỏi: <span class="mathjax"> <?= htmlspecialchars($row['question']) ?> </span></div>

        <?php if (!empty($row['image'])): ?>
          <img src="<?= htmlspecialchars($row['image']) ?>" alt="Ảnh minh họa">
        <?php endif; ?>

        <ul>
          <li>Đáp án 1: <?= htmlspecialchars($row['answer1']) ?> <?= $row['correct_answer'] == 'answer1' ? '<span class="correct">(Đúng)</span>' : '' ?></li>
          <li>Đáp án 2: <?= htmlspecialchars($row['answer2']) ?> <?= $row['correct_answer'] == 'answer2' ? '<span class="correct">(Đúng)</span>' : '' ?></li>
          <li>Đáp án 3: <?= htmlspecialchars($row['answer3']) ?> <?= $row['correct_answer'] == 'answer3' ? '<span class="correct">(Đúng)</span>' : '' ?></li>
          <li>Đáp án 4: <?= htmlspecialchars($row['answer4']) ?> <?= $row['correct_answer'] == 'answer4' ? '<span class="correct">(Đúng)</span>' : '' ?></li>
        </ul>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>Chưa có câu hỏi nào.</p>
  <?php endif; ?>

  <script>
    MathJax.typeset(); // Kích hoạt MathJax nếu có LaTeX
  </script>
</body>
</html>

<?php $conn->close(); ?>
