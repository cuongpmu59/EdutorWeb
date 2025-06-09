<?php
require 'db_connection.php';

$stmt = $conn->query("SELECT * FROM questions ORDER BY id DESC");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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
    <tr onclick='fillForm(<?php echo json_encode($q); ?>)'>
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

<script>
  // Hàm fillForm khi click bảng trong fetch_questions.php
  function fillForm(data) {
    document.getElementById('question_id').value = data.id;
    document.querySelector('textarea[name="question"]').value = data.question;
    document.querySelector('input[name="answer1"]').value = data.answer1;
    document.querySelector('input[name="answer2"]').value = data.answer2;
    document.querySelector('input[name="answer3"]').value = data.answer3;
    document.querySelector('input[name="answer4"]').value = data.answer4;
    document.querySelector('select[name="correct_answer"]').value = data.correct
