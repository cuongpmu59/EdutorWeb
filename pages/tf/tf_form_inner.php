<form id="tf_form">
  <!-- ID ẩn -->
  <input type="hidden" id="tf_id">

  <!-- Chủ đề -->
  <label for="tf_topic">📚 Chủ đề:</label>
  <input type="text" id="tf_topic" class="form-control" placeholder="Nhập chủ đề..." required>

  <!-- Đề bài chính -->
  <label for="tf_question">🧠 Đề bài chính:</label>
  <textarea id="tf_question" class="form-control" rows="3" placeholder="Nhập đề bài (có thể dùng LaTeX như \\(x^2 + y^2 = z^2\\))" required></textarea>

  <!-- Mệnh đề 1 -->
  <label for="tf_statement1">➊ Mệnh đề 1:</label>
  <input type="text" id="tf_statement1" class="form-control" placeholder="Nhập mệnh đề 1">

  <label for="tf_correct_answer1">Đáp án đúng 1:</label>
  <select id="tf_correct_answer1" class="form-control">
    <option value="">-- Chọn --</option>
    <option value="Đúng">Đúng</option>
    <option value="Sai">Sai</option>
  </select>

  <!-- Mệnh đề 2 -->
  <label for="tf_statement2">➋ Mệnh đề 2:</label>
  <input type="text" id="tf_statement2" class="form-control" placeholder="Nhập mệnh đề 2">

  <label for="tf_correct_answer2">Đáp án đúng 2:</label>
  <select id="tf_correct_answer2" class="form-control">
    <option value="">-- Chọn --</option>
    <option value="Đúng">Đúng</option>
    <option value="Sai">Sai</option>
  </select>

  <!-- Mệnh đề 3 -->
  <label for="tf_statement3">➌ Mệnh đề 3:</label>
  <input type="text" id="tf_statement3" class="form-control" placeholder="Nhập mệnh đề 3">

  <label for="tf_correct_answer3">Đáp án đúng 3:</label>
  <select id="tf_correct_answer3" class="form-control">
    <option value="">-- Chọn --</option>
    <option value="Đúng">Đúng</option>
    <option value="Sai">Sai</option>
  </select>

  <!-- Đáp án đúng bổ sung (nếu có) -->
  <label for="tf_correct_answer4">🔎 Đáp án đúng bổ sung (nếu có):</label>
  <input type="text" id="tf_correct_answer4" class="form-control" placeholder="Nhập đáp án đúng khác nếu cần">

  <!-- Nút chức năng -->
  <div style="margin-top: 15px;">
    <button type="button" id="tf_saveBtn" class="btn-save">💾 Lưu</button>
    <button type="button" id="tf_resetBtn" class="btn-reset">🔄 Làm mới</button>
    <button type="button" id="tf_deleteBtn" class="btn-delete">🗑️ Xoá</button>
  </div>

  <!-- Xem trước -->
  <div id="tf_preview_content" class="preview-box" style="margin-top: 20px;"></div>
</form>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const $ = id => document.getElementById(id);

  function updatePreview() {
    let html = `<strong>📚 Chủ đề:</strong> ${$("tf_topic").value}<br>`;
    html += `<strong>🧠 Đề bài:</strong><br>${$("tf_question").value}<br><br>`;

    for (let i = 1; i <= 3; i++) {
      const st = $(`tf_statement${i}`).value;
      const ans = $(`tf_correct_answer${i}`).value;
      if (st) {
        html += `➊ Mệnh đề ${i}: ${st} <em>(${ans})</em><br>`;
      }
    }

    const extra = $("tf_correct_answer4").value;
    if (extra) html += `<strong>Đáp án bổ sung:</strong> ${extra}<br>`;

    $("tf_preview_content").innerHTML = html;
    if (window.MathJax) MathJax.typesetPromise();
  }

  // Gán sự kiện cập nhật xem trước
  [
    "tf_topic", "tf_question", "tf_statement1", "tf_correct_answer1",
    "tf_statement2", "tf_correct_answer2",
    "tf_statement3", "tf_correct_answer3",
    "tf_correct_answer4"
  ].forEach(id => {
    $(id).addEventListener("input", updatePreview);
    $(id).addEventListener("change", updatePreview);
  });

  // Làm mới form
  $("tf_resetBtn").addEventListener("click", () => {
    ["tf_id", "tf_topic", "tf_question", "tf_statement1", "tf_statement2", "tf_statement3", "tf_correct_answer4"].forEach(id => $(id).value = "");
    ["tf_correct_answer1", "tf_correct_answer2", "tf_correct_answer3"].forEach(id => $(id).value = "");
    $("tf_preview_content").innerHTML = "";
  });

  // Lưu
  $("tf_saveBtn").addEventListener("click", () => {
    const data = {
      tf_id: $("tf_id").value,
      tf_topic: $("tf_topic").value.trim(),
      tf_question: $("tf_question").value.trim(),
      tf_statement1: $("tf_statement1").value.trim(),
      tf_correct_answer1: $("tf_correct_answer1").value,
      tf_statement2: $("tf_statement2").value.trim(),
      tf_correct_answer2: $("tf_correct_answer2").value,
      tf_statement3: $("tf_statement3").value.trim(),
      tf_correct_answer3: $("tf_correct_answer3").value,
      tf_correct_answer4: $("tf_correct_answer4").value.trim()
    };

    if (!data.tf_topic || !data.tf_question) {
      alert("Vui lòng nhập đầy đủ chủ đề và câu hỏi.");
      return;
    }

    // Gửi dữ liệu AJAX (tuỳ bạn xử lý)
    console.log("Dữ liệu cần lưu:", data);
  });

  // Xoá
  $("tf_deleteBtn").addEventListener("click", () => {
    const id = $("tf_id").value;
    if (id && confirm("Bạn có chắc muốn xoá câu hỏi này không?")) {
      console.log("Xoá câu hỏi ID =", id);
    }
  });
});
</script>
