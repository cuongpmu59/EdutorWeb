<!-- pages/mc/mc_form_inner.php -->
<div class="mc-form-container card shadow-xl p-4 rounded-2xl bg-light dark:bg-dark">
  <form id="mcForm" class="question-form" method="POST" action="mc_insert.php" enctype="multipart/form-data">
    <input type="hidden" id="mc_id" name="mc_id" value="">

    <!-- Chủ đề -->
    <div class="form-group">
      <label for="mc_topic">📚 Chủ đề:</label>
      <input type="text" id="mc_topic" name="mc_topic" class="form-control" placeholder="Nhập tên chủ đề..." required>
    </div>

    <!-- Câu hỏi -->
    <div class="form-group">
      <label for="mc_question">🧠 Câu hỏi:</label>
      <textarea id="mc_question" name="mc_question" rows="3" class="form-control" placeholder="Nhập nội dung câu hỏi..." required></textarea>
    </div>

    <!-- Xem trước công thức -->
    <div class="form-group">
      <label for="previewFormulaInput">📌 Xem trước công thức (LaTeX):</label>
      <textarea id="previewFormulaInput" rows="2" class="form-control" placeholder="Ví dụ: \\( a^2 + b^2 = c^2 \\)"></textarea>
      <div id="previewFormulaOutput" class="preview-box mt-2 p-3 border border-dashed bg-white dark:bg-gray-800 rounded shadow-sm"></div>
    </div>

    <!-- Đáp án -->
    <div class="form-group">
      <label for="mc_answer1">🔠 Đáp án 1 (A):</label>
      <input type="text" id="mc_answer1" name="mc_answer1" class="form-control" required>
    </div>
    <div class="form-group">
      <label for="mc_answer2">🔠 Đáp án 2 (B):</label>
      <input type="text" id="mc_answer2" name="mc_answer2" class="form-control" required>
    </div>
    <div class="form-group">
      <label for="mc_answer3">🔠 Đáp án 3 (C):</label>
      <input type="text" id="mc_answer3" name="mc_answer3" class="form-control" required>
    </div>
    <div class="form-group">
      <label for="mc_answer4">🔠 Đáp án 4 (D):</label>
      <input type="text" id="mc_answer4" name="mc_answer4" class="form-control" required>
    </div>

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

    <!-- Cảnh báo -->
    <div id="formWarning" class="form-warning alert alert-warning" style="display: none;">
      ⚠️ Vui lòng nhập đầy đủ tất cả các trường bắt buộc.
    </div>

    <!-- Nút điều khiển -->
    <div class="form-actions mt-3 flex flex-wrap gap-2">
      <button type="submit" class="btn btn-primary">💾 Lưu</button>
      <button type="reset" class="btn btn-secondary">🔄 Làm mới</button>
      <button type="button" id="deleteBtn" class="btn btn-danger" style="display: none;">🗑️ Xoá</button>
    </div>
  </form>
</div>

<!-- Script xử lý -->
<script type="module">
  import { updateLivePreview } from "../../js/modules/mathPreview.js";

  const form = document.getElementById("mcForm");

  form.addEventListener("submit", function (e) {
    const fields = [
      "mc_topic", "mc_question", "mc_answer1", "mc_answer2",
      "mc_answer3", "mc_answer4", "mc_correct_answer"
    ];
    let valid = true;
    for (const id of fields) {
      const el = document.getElementById(id);
      if (!el || !el.value.trim()) {
        valid = false;
        break;
      }
    }
    if (!valid) {
      e.preventDefault();
      document.getElementById("formWarning").style.display = "block";
    } else {
      document.getElementById("formWarning").style.display = "none";
    }
  });

  // Xem trước công thức toán
  const formulaInput = document.getElementById("previewFormulaInput");
  const formulaOutput = document.getElementById("previewFormulaOutput");

  if (formulaInput && formulaOutput) {
    formulaInput.addEventListener("input", () => {
      updateLivePreview(formulaInput, formulaOutput);
    });
  }
</script>
