<form id="trueFalseForm" method="POST" action="insert_true_false_question.php">
  <div>
    <label>📚 Chủ đề:</label>
    <input type="text" id="topic" placeholder="Nhập chủ đề..." class="form-control" required>
  </div>

  <div style="margin-top: 10px;">
    <label>🧠 Đề bài chính:</label>
    <textarea id="main_question" rows="3" placeholder="Nhập đề bài chính..." class="form-control" required></textarea>
  </div>

  <hr>
  <?php for ($i = 1; $i <= 4; $i++): ?>
    <div style="margin-bottom: 15px;">
      <label>Ý <?= $i ?>:</label>
      <textarea id="statement<?= $i ?>" rows="2" class="form-control" placeholder="Nhập nội dung ý <?= $i ?>" required></textarea>

      <div style="margin-top: 5px;">
        <label>Đáp án đúng:</label><br>
        <label><input type="radio" name="correct_answer<?= $i ?>" value="1"> ✅ Đúng</label>
        <label style="margin-left: 20px;"><input type="radio" name="correct_answer<?= $i ?>" value="0" checked> ❌ Sai</label>
      </div>
    </div>
  <?php endfor; ?>
  <hr>

  <!-- Hidden inputs để submit từ localStorage -->
  <input type="hidden" name="topic" id="hidden_topic">
  <input type="hidden" name="main_question" id="hidden_question">
  <input type="hidden" name="image_url" id="hidden_image">

  <?php for ($i = 1; $i <= 4; $i++): ?>
    <input type="hidden" name="statement<?= $i ?>" id="hidden_statement<?= $i ?>">
    <input type="hidden" name="correct_answer<?= $i ?>" id="hidden_correct<?= $i ?>">
  <?php endfor; ?>

  <button type="submit" class="btn btn-primary">💾 Lưu câu hỏi</button>
</form>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // 1. Đồng bộ khi người dùng nhập
  document.getElementById("topic").addEventListener("input", () => {
    localStorage.setItem("true_false_topic", document.getElementById("topic").value);
  });

  document.getElementById("main_question").addEventListener("input", () => {
    localStorage.setItem("true_false_main_question", document.getElementById("main_question").value);
  });

  for (let i = 1; i <= 4; i++) {
    document.getElementById("statement" + i).addEventListener("input", () => {
      localStorage.setItem("statement" + i, document.getElementById("statement" + i).value);
    });

    document.querySelectorAll(`[name=correct_answer${i}]`).forEach(radio => {
      radio.addEventListener("change", () => {
        localStorage.
