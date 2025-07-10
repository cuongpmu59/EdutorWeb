<!-- pages/mc/mc_form_inner.php -->
<div class="mc-form-container">
  <form id="mcForm" class="question-form" method="POST" action="mc_insert.php" enctype="multipart/form-data">
    <!-- ID (ẩn khi sửa) -->
    <input type="hidden" id="mc_id" name="mc_id" value="">

    <!-- Chủ đề -->
    <div class="form-group">
      <label for="mc_topic">📚 Chủ đề:</label>
      <input type="text" id="mc_topic" name="mc_topic" class="form-control" required>
    </div>

    <!-- Câu hỏi -->
    <div class="form-group">
      <label for="mc_question">🧠 Câu hỏi:</label>
      <textarea id="mc_question" name="mc_question" rows="3" class="form-control" required></textarea>
    </div>

    <!-- Ảnh minh hoạ -->
    <div class="form-group">
      <label for="mc_image_url">🖼️ Ảnh minh hoạ (tuỳ chọn):</label>
      <input type="file" id="mc_image_input" accept="image/*" class="form-control">
      <input type="hidden" id="mc_image_url" name="mc_image_url">
      <img id="mc_image_preview" src="" style="display:none; max-height: 150px; margin-top:10px;">
      <button type="button" id="deleteImageBtn" class="btn-danger" style="display:none;">🗑️ Xoá ảnh</button>
    </div>

    <!-- Các đáp án -->
    <div class="form-group"><label for="mc_answer1">🔠 Đáp án 1 (A):</label><input type="text" id="mc_answer1" name="mc_answer1" class="form-control" required></div>
    <div class="form-group"><label for="mc_answer2">🔠 Đáp án 2 (B):</label><input type="text" id="mc_answer2" name="mc_answer2" class="form-control" required></div>
    <div class="form-group"><label for="mc_answer3">🔠 Đáp án 3 (C):</label><input type="text" id="mc_answer3" name="mc_answer3" class="form-control" required></div>
    <div class="form-group"><label for="mc_answer4">🔠 Đáp án 4 (D):</label><input type="text" id="mc_answer4" name="mc_answer4" class="form-control" required></div>

    <!-- Đáp án đúng -->
    <div class="form-group">
      <label for="mc_correct_answer">✅ Đáp án đúng:</label>
      <select id="mc_correct_answer" name="mc_correct_answer" class="form-control" required>
        <option value="">-- Chọn đáp án đúng --</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
      </select>
    </div>

    <!-- Cảnh báo nếu thiếu -->
    <div id="formWarning" class="form-warning" style="display: none;">
      ⚠️ Vui lòng nhập đầy đủ tất cả các trường bắt buộc.
    </div>

    <!-- Nút hành động -->
    <div class="form-actions">
      <button type="submit" class="btn-primary">💾 Lưu</button>
      <button type="reset" class="btn-secondary">🔄 Làm mới</button>
      <button type="button" class="btn-danger" id="deleteBtn" style="display: none;">🗑️ Xoá</button>
    </div>
  </form>
</div>

<!-- Script xử lý validation -->
<script>
  document.getElementById("mcForm").addEventListener("submit", function (e) {
    const requiredFields = [
      "mc_topic", "mc_question", "mc_answer1", "mc_answer2",
      "mc_answer3", "mc_answer4", "mc_correct_answer"
    ];
    let isValid = true;
    for (const id of requiredFields) {
      const el = document.getElementById(id);
      if (!el || !el.value.trim()) {
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
