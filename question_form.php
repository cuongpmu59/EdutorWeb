<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      vertical-align: top;
    }
    textarea, input[type="text"] {
      width: 100%;
      margin-bottom: 10px;
      padding: 6px;
    }
    img {
      max-width: 120px;
      max-height: 120px;
    }
  </style>

  <!-- MathJax -->
  <script>
    window.MathJax = {
      tex: { inlineMath: [['\\(', '\\)'], ['$', '$']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<h2>Thêm / Sửa Câu Hỏi</h2>
<form action="insert_question.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="id" id="id">

  <label>Câu hỏi (Latex hỗ trợ):</label>
  <textarea name="question" id="question" required></textarea>

  <label>Ảnh minh họa:</label>
  <input type="file" name="image" accept="image/*"><br>
  <img id="previewImage" style="display:none;"><br>

  <label>Đáp án 1:</label>
  <input type="text" name="answer1" id="answer1" required>

  <label>Đáp án 2:</label>
  <input type="text" name="answer2" id="answer2" required>

  <label>Đáp án 3:</label>
  <input type="text" name="answer3" id="answer3" required>

  <label>Đáp án 4:</label>
  <input type="text" name="answer4" id="answer4" required>

  <label>Đáp án đúng (1-4):</label>
  <input type="text" name="correct_answer" id="correct_answer" required>

  <button type="submit">Lưu câu hỏi</button>
</form>

<hr>

<h3>Danh sách câu hỏi</h3>

<?php
include 'db_connection.php';
$sql = "SELECT * FROM questions ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  echo "<table><tr>
    <th>ID</th><th>Câu hỏi</th><th>Ảnh</th>
    <th>Đáp án 1</th><th>Đáp án 2</th><th>Đáp án 3</th><th>Đáp án 4</th><th>Đúng</th>
  </tr>";
  while ($row = $result->fetch_assoc()) {
    $img = $row["image"] ? "<img src='{$row["image"]}'>" : "";
    echo "<tr onclick='fillForm(this)' 
      data-id='{$row["id"]}' 
      data-question=\"" . htmlspecialchars($row["question"], ENT_QUOTES) . "\"
      data-image='{$row["image"]}' 
      data-answer1=\"" . htmlspecialchars($row["answer1"], ENT_QUOTES) . "\"
      data-answer2=\"" . htmlspecialchars($row["answer2"], ENT_QUOTES) . "\"
      data-answer3=\"" . htmlspecialchars($row["answer3"], ENT_QUOTES) . "\"
      data-answer4=\"" . htmlspecialchars($row["answer4"], ENT_QUOTES) . "\"
      data-correct='{$row["correct_answer"]}'>
      <td>{$row["id"]}</td>
      <td class='math'>{$row["question"]}</td>
      <td>$img</td>
      <td class='math'>{$row["answer1"]}</td>
      <td class='math'>{$row["answer2"]}</td>
      <td class='math'>{$row["answer3"]}</td>
      <td class='math'>{$row["answer4"]}</td>
      <td>{$row["correct_answer"]}</td>
    </tr>";
  }
  echo "</table>";
} else {
  echo "Chưa có câu hỏi nào.";
}
$conn->close();
?>

<script>
function fillForm(row) {
  document.getElementById('id').value = row.dataset.id;
  document.getElementById('question').value = row.dataset.question;
  document.getElementById('answer1').value = row.dataset.answer1;
  document.getElementById('answer2').value = row.dataset.answer2;
  document.getElementById('answer3').value = row.dataset.answer3;
  document.getElementById('answer4').value = row.dataset.answer4;
  document.getElementById('correct_answer').value = row.dataset.correct;

  const img = document.getElementById('previewImage');
  if (row.dataset.image) {
    img.src = row.dataset.image;
    img.style.display = 'block';
  } else {
    img.style.display = 'none';
  }
  if (window.MathJax) MathJax.typeset();
}
</script>

</body>
</html>
