<?php
require 'db_connection.php';

// Lấy danh sách câu hỏi từ CSDL
$stmt = $conn->query("SELECT * FROM questions ORDER BY id DESC");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<title>Quản lý câu hỏi</title>

<!-- Thư viện MathJax -->
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<style>
  /* CSS đơn giản cho bảng */
  table {
    border-collapse: collapse;
    width: 100%;
  }
  th, td {
    border: 1px solid #ccc;
    padding: 8px;
  }
  th {
    background-color: #f2f2f2;
  }
</style>

<script src="js/question_script.js"></script>

</head>
<body>

<h2>Form nhập câu hỏi</h2>
<form id="questionForm" enctype="multipart/form-data">
  <input type="hidden" id="question_id" name="id" value="">
  <label>Câu hỏi: <br />
    <textarea name="question" id="question" rows="3" required></textarea>
  </label><br />
  <label>Đáp án A: <input type="text" name="answer1" required></label><br />
  <label>Đáp án B: <input type="text" name="answer2" required></label><br />
  <label>Đáp án C: <input type="text" name="answer3"></label><br />
  <label>Đáp án D: <input type="text" name="answer4"></label><br />
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

<hr />

<h2>Danh sách câu hỏi</h2>
<div id="questionTableContainer">
  <table id="questionTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Câu hỏi</th>
        <th>Đáp án A</th>
        <th>Đáp án B</th>
        <th>Đáp án C</th>
        <th>Đáp án D</th>
        <th>Đáp án đúng</th>
        <th>Ảnh</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($questions as $q): ?>
      <tr onclick="fillForm(<?php echo htmlspecialchars(json_encode($q)); ?>)">
        <td><?php echo $q['id']; ?></td>
        <td><?php echo htmlspecialchars($q['question']); ?></td>
        <td><?php echo htmlspecialchars($q['answer1']); ?></td>
        <td><?php echo htmlspecialchars($q['answer2']); ?></td>
        <td><?php echo htmlspecialchars($q['answer3']); ?></td>
        <td><?php echo htmlspecialchars($q['answer4']); ?></td>
        <td><?php echo htmlspecialchars($q['correct_answer']); ?></td>
        <td>
          <?php if ($q['image']): ?>
            <img src="images/<?php echo htmlspecialchars($q['image']); ?>" alt="Ảnh" style="max-width:100px;">
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
  // Hiển thị preview ảnh khi chọn file
  document.getElementById('image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const preview = document.getElementById('imagePreview');
        preview.src = e.target.result;
        preview.style.display = 'block';
      }
      reader.readAsDataURL(file);
    }
  });

  // Điền dữ liệu câu hỏi vào form khi click bảng
  function fillForm(data) {
    document.getElementById('question_id').value = data.id;
    document.querySelector('textarea[name="question"]').value = data.question;
    document.querySelector('input[name="answer1"]').value = data.answer1;
    document.querySelector('input[name="answer2"]').value = data.answer2;
    document.querySelector('input[name="answer3"]').value = data.answer3;
    document.querySelector('input[name="answer4"]').value = data.answer4;
    document.querySelector('select[name="correct_answer"]').value = data.correct_answer;

    if(data.image) {
      const preview = document.getElementById('imagePreview');
      preview.src = 'images/' + data.image;
      preview.style.display = 'block';
    } else {
      document.getElementById('imagePreview').style.display = 'none';
    }
  }

  // Ẩn / hiện bảng câu hỏi
  function toggleTable() {
    const container = document.getElementById('questionTableContainer');
    container.style.display = container.style.display === 'none' ? 'block' : 'none';
  }

  // Sau khi load trang, gọi MathJax render công thức
  window.onload = () => {
    MathJax.typesetPromise();
  };
</script>

</body>
</html>
