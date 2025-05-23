<?php
// Kết nối CSDL
$host = "sql210.infinityfree.com"; 
$username = "if0_39047715";
$password = "Kimdung16091961";
$dbname = "if0_39047715_questionbank";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Lỗi kết nối CSDL: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 30px;
      max-width: 800px;
    }

    label {
      font-weight: bold;
      margin-top: 15px;
      display: block;
    }

    textarea,
    input[type="text"],
    select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      font-size: 16px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    button {
      margin-top: 20px;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 6px;
      border: none;
    }

    button[type="submit"] {
      background-color: #1976d2;
      color: white;
    }

    button[type="reset"] {
      background-color: #e53935;
      color: white;
    }

    button:hover {
      opacity: 0.9;
    }

    .question-list {
      margin-top: 40px;
      border-top: 2px solid #ccc;
      padding-top: 20px;
    }

    .question-item {
      border-bottom: 1px solid #ddd;
      margin-bottom: 20px;
      padding-bottom: 10px;
    }

    .question-item img {
      max-width: 200px;
      margin-top: 5px;
    }

    .correct {
      color: green;
      font-weight: bold;
    }

    small {
      color: #666;
    }
  </style>
</head>

<body>
  <h1>Nhập câu hỏi trắc nghiệm</h1>

  <form action="save_question.php" method="POST" enctype="multipart/form-data" id="questionForm">
    <label for="question">Câu hỏi:</label>
    <small>Hỗ trợ công thức toán học với LaTeX, ví dụ: \\( a^2 + b^2 = c^2 \\)</small>
    <textarea id="question" name="question" rows="6" required placeholder="Nhập câu hỏi ở đây..."></textarea>

    <label for="image">Ảnh minh họa (nếu có):</label>
    <input type="file" id="image" name="image" accept="image/*">
    <img id="imagePreview" style="display: none; margin-top: 10px; border: 1px solid #ccc; border-radius: 6px; max-width: 150px;" />

    <label for="answer1">Đáp án 1:</label>
    <input type="text" id="answer1" name="answer1" required>

    <label for="answer2">Đáp án 2:</label>
    <input type="text" id="answer2" name="answer2" required>

    <label for="answer3">Đáp án 3:</label>
    <input type="text" id="answer3" name="answer3">

    <label for="answer4">Đáp án 4:</label>
    <input type="text" id="answer4" name="answer4">

    <label for="correct_answer">Chọn đáp án đúng:</label>
    <select id="correct_answer" name="correct_answer" required>
      <option value="">--Chọn đáp án đúng--</option>
      <option value="answer1">Đáp án 1</option>
      <option value="answer2">Đáp án 2</option>
      <option value="answer3">Đáp án 3</option>
      <option value="answer4">Đáp án 4</option>
    </select>

    <button type="submit">Lưu câu hỏi</button>
    <button type="reset">Xóa dữ liệu</button>
  </form>

  <div id="preview" style="margin-top: 20px; padding: 10px; border: 1px dashed #ccc;"></div>

  <div class="question-list">
    <h2>Danh sách câu hỏi đã lưu</h2>

    <?php
    $sql = "SELECT * FROM questions ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
    ?>
        <div class="question-item">
          <div><strong>Câu hỏi:</strong> <span class="mathjax"><?= htmlspecialchars($row['question']) ?></span></div>
          <?php if (!empty($row['image'])): ?>
            <img src="<?= htmlspecialchars($row['image']) ?>" alt="Ảnh minh họa">
          <?php endif; ?>
          <ul>
            <?php for ($i = 1; $i <= 4; $i++):
              $ans = htmlspecialchars($row["answer$i"]);
              if ($ans): ?>
              <li>Đáp án <?= $i ?>: <?= $ans ?>
                <?php if ($row['correct_answer'] == "answer$i") echo '<span class="correct">(Đúng)</span>'; ?>
              </li>
            <?php endif; endfor; ?>
          </ul>
        </div>
    <?php endwhile; else: ?>
      <p>Chưa có câu hỏi nào.</p>
    <?php endif;
    $conn->close();
    ?>
  </div>

  <script>
    // Xem trước LaTeX
    const questionInput = document.getElementById('question');
    const preview = document.getElementById('preview');
    questionInput.addEventListener('input', () => {
      preview.innerHTML = questionInput.value;
      MathJax.typesetPromise([preview]);
    });

    // Xem trước ảnh
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    imageInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        imagePreview.src = URL.createObjectURL(file);
        imagePreview.style.display = "block";
      } else {
        imagePreview.style.display = "none";
      }
    });

    // Kiểm tra form khi chọn đáp án 3 hoặc 4 mà không nhập nội dung
    const form = document.getElementById('questionForm');
    form.addEventListener('submit', function (e) {
      const correct = document.getElementById('correct_answer').value;
      const ans3 = form.answer3.value.trim();
      const ans4 = form.answer4.value.trim();
      if ((correct === 'answer3' && !ans3) || (correct === 'answer4' && !ans4)) {
        alert("Bạn đã chọn đáp án đúng là 3 hoặc 4 nhưng chưa nhập nội dung cho đáp án đó.");
        e.preventDefault();
      }
    });
  </script>
</body>

</html>
