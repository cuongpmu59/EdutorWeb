<?php
// question_form.php
require 'db_connection.php';
$stmt = $conn->query("SELECT * FROM questions ORDER BY id DESC");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<title>Quản lý câu hỏi</title>

<!-- MathJax -->
<script>
  window.MathJax = {
    tex: { inlineMath: [['\\(', '\\)'], ['$', '$']] },
    svg: { fontCache: 'global' }
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" defer></script>

<style>
  table { border-collapse: collapse; width: 100%; }
  th, td { border: 1px solid #ccc; padding: 8px; }
  th { background-color: #f2f2f2; }
</style>
<script src="js/question_script.js"></script>
</head>
<body>

<h2>Form nhập câu hỏi</h2>
<form id="questionForm" enctype="multipart/form-data">
  <input type="hidden" id="question_id" name="id" value="">
  <label>Câu hỏi (hỗ trợ LaTeX):<br />
    <textarea name="question" id="question" rows="3" required oninput="renderPreview()"></textarea>
  </label><br />
  <label>Đáp án A: <input type="text" name="answer1" oninput="renderPreview()" required></label><br />
  <label>Đáp án B: <input type="text" name="answer2" oninput="renderPreview()" required></label><br />
  <label>Đáp án C: <input type="text" name="answer3" oninput="renderPreview()"></label><br />
  <label>Đáp án D: <input type="text" name="answer4" oninput="renderPreview()"></label><br />
  <label>Đáp án đúng:
    <select name="correct_answer" required>
      <option value="answer1">A</option>
      <option value="answer2">B</option>
      <option value="answer3">C</option>
      <option value="answer4">D</option>
    </select>
  </label><br />
  <label>Ảnh (nếu có): <input type="file" name="image" id="image"></label><br />
  <img id="imagePreview" src="#" alt="Preview ảnh" style="max-width: 150px; display:none;" /><br />

  <button type="button" onclick="saveQuestion()">Lưu</button>
  <button type="button" onclick="deleteQuestion()">Xoá</button>
  <button type="button" onclick="searchQuestion()">Tìm kiếm</button>
  <button type="button" onclick="toggleTable()">Ẩn/Hiện bảng câu hỏi</button>
</form>

<hr>
<h3>Xem trước công thức</h3>
<div id="latexPreview" style="border:1px solid #ccc; padding:10px; background:#f9f9f9;"></div>

<hr />
<h2>Danh sách câu hỏi</h2>
<div id="questionTableContainer">
  <table id="questionTable">
    <thead>
      <tr>
        <th>ID</th><th>Câu hỏi</th><th>Đáp án A</th><th>Đáp án B</th>
        <th>Đáp án C</th><th>Đáp án D</th><th>Đáp án đúng</th><th>Ảnh</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($questions as $q): ?>
      <tr onclick='fillForm(<?php echo json_encode($q); ?>)'>
        <td><?= $q['id'] ?></td>
        <td><?= htmlspecialchars($q['question']) ?></td>
        <td><?= htmlspecialchars($q['answer1']) ?></td>
        <td><?= htmlspecialchars($q['answer2']) ?></td>
        <td><?= htmlspecialchars($q['answer3']) ?></td>
        <td><?= htmlspecialchars($q['answer4']) ?></td>
        <td><?= strtoupper(substr($q['correct_answer'], -1)) ?></td>
        <td><?php if ($q['image']): ?><img src="images/<?= htmlspecialchars($q['image']) ?>" style="max-width:100px;" /><?php endif; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
function renderPreview() {
  const preview = document.getElementById("latexPreview");
  const question = document.getElementById("question").value;
  const a = document.querySelector('input[name="answer1"]').value;
  const b = document.querySelector('input[name="answer2"]').value;
  const c = document.querySelector('input[name="answer3"]').value;
  const d = document.querySelector('input[name="answer4"]').value;
  preview.innerHTML = `<b>Câu hỏi:</b> ${question}<br><b>A:</b> ${a}<br><b>B:</b> ${b}<br><b>C:</b> ${c}<br><b>D:</b> ${d}`;
  MathJax.typesetPromise();
}

function fillForm(data) {
  document.getElementById('question_id').value = data.id;
  document.querySelector('textarea[name="question"]').value = data.question;
  document.querySelector('input[name="answer1"]').value = data.answer1;
  document.querySelector('input[name="answer2"]').value = data.answer2;
  document.querySelector('input[name="answer3"]').value = data.answer3;
  document.querySelector('input[name="answer4"]').value = data.answer4;
  document.querySelector('select[name="correct_answer"]').value = data.correct_answer;
  if (data.image) {
    document.getElementById('imagePreview').src = 'images/' + data.image;
    document.getElementById('imagePreview').style.display = 'block';
  } else {
    document.getElementById('imagePreview').style.display = 'none';
  }
  renderPreview();
}

window.onload = () => {
  renderPreview();
  MathJax.typesetPromise();
};
</script>
</body>
</html>
