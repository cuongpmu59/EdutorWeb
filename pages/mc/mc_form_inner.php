<form id="mcForm" class="question-form" method="POST" action="insert_question.php">
  <!-- Hidden: ID câu hỏi khi sửa -->
  <input type="hidden" id="question_id" name="id" value="">

  <!-- Chủ đề -->
  <div class="form-group">
    <label for="topic">📚 Chủ đề:</label>
    <input type="text" id="topic" name="topic" class="form-control" required>
  </div>

  <!-- Câu hỏi -->
  <div class="form-group">
    <label for="question">🧠 Câu hỏi:</label>
    <textarea id="question" name="question" rows="3" class="form-control" required></textarea>
  </div>

  <!-- Đáp án A -->
  <div class="form-group">
    <label for="optionA">🔠 A.</label>
    <input type="text" id="optionA" name="optionA" class="form-control" required>
  </div>

  <!-- Đáp án B -->
  <div class="form-group">
    <label for="optionB">🔠 B.</label>
    <input type="text" id="optionB" name="optionB" class="form-control" required>
  </div>

  <!-- Đáp án C -->
  <div class="form-group">
    <label for="optionC">🔠 C.</label>
    <input type="text" id="optionC" name="optionC" class="form-control" required>
  </div>

  <!-- Đáp án D -->
  <div class="form-group">
    <label for="optionD">🔠 D.</label>
    <input type="text" id="optionD" name="optionD" class="form-control" required>
  </div>

  <!-- Đáp án đúng -->
  <div class="form-group">
    <label for="correctAnswer">✅ Đáp án đúng:</label>
    <select id="correctAnswer" name="correctAnswer" class="form-control" required>
      <option value="">-- Chọn đáp án đúng --</option>
      <option value="A">A</option>
      <option value="B">B</option>
      <option value="C">C</option>
      <option value="D">D</option>
    </select>
  </div>

  <!-- Cảnh báo nếu thiếu -->
  <div id="formWarning" class="form-warning" style="display: none;">
    ⚠️ Vui lòng nhập đầy đủ tất cả các trường trước khi lưu.
  </div>

  <!-- Nút hành động -->
  <div class="form-actions">
    <button type="submit" class="btn-primary">💾 Lưu</button>
    <button type="reset" class="btn-secondary">🔄 Làm mới</button>
    <button type="button" class="btn-danger" id="deleteBtn" style="display: none;">🗑️ Xoá</button>
  </div>
</form>

<script>
  // Hiển thị cảnh báo nếu form thiếu trường bắt buộc
  document.getElementById("mcForm").addEventListener("submit", function (e) {
    const fields = ["topic", "question", "optionA", "optionB", "optionC", "optionD", "correctAnswer"];
    let isValid = true;

    for (const id of fields) {
      if (!document.getElementById(id).value.trim()) {
        isValid = false;
        break;
      }
    }

    if (!isValid) {
      e.preventDefault();
      document.getElementById("formWarning").style.display = "block";
    } else {
      document.getElementById("formWarning").style.display = "none";
    }
  });
</script>
