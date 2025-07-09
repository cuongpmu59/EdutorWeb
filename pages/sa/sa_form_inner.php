<form id="sa_form">
  <!-- ID câu hỏi ẩn -->
  <input type="hidden" id="sa_id">

  <!-- Chủ đề -->
  <label for="sa_topic">📚 Chủ đề:</label>
  <input type="text" id="sa_topic" class="form-control" placeholder="Nhập chủ đề..." required>

  <!-- Câu hỏi -->
  <label for="sa_question">🧠 Câu hỏi:</label>
  <textarea id="sa_question" class="form-control" rows="5" placeholder="Nhập câu hỏi (có thể chứa công thức LaTeX như \\( a^2 + b^2 = c^2 \\))" required></textarea>

  <!-- Nút hành động -->
  <div style="margin-top: 15px;">
    <button type="button" id="sa_saveBtn" class="btn-save">💾 Lưu</button>
    <button type="button" id="sa_resetBtn" class="btn-reset">🔄 Làm mới</button>
    <button type="button" id="sa_deleteBtn" class="btn-delete">🗑️ Xoá</button>
  </div>

  <!-- Hộp xem trước -->
  <div id="sa_preview_content" class="preview-box" style="margin-top: 20px;"></div>
</form>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const $ = id => document.getElementById(id);

  // Cập nhật xem trước khi nhập câu hỏi
  $("sa_question").addEventListener("input", () => {
    $("sa_preview_content").innerHTML = $("sa_question").value;
    if (window.MathJax) MathJax.typesetPromise();
  });

  // Nút Làm mới
  $("sa_resetBtn").addEventListener("click", () => {
    $("sa_id").value = "";
    $("sa_topic").value = "";
    $("sa_question").value = "";
    $("sa_preview_content").innerHTML = "";
  });

  // Nút Lưu (gọi sự kiện tuỳ bạn xử lý bên ngoài)
  $("sa_saveBtn").addEventListener("click", () => {
    const data = {
      id: $("sa_id").value,
      topic: $("sa_topic").value.trim(),
      question: $("sa_question").value.trim()
    };

    if (!data.topic || !data.question) {
      alert("Vui lòng nhập đầy đủ Chủ đề và Câu hỏi!");
      return;
    }

    // Gửi dữ liệu đi (gọi AJAX tùy theo hệ thống của bạn)
    console.log("Đã sẵn sàng gửi dữ liệu:", data);

    // Gợi ý: gọi hàm saveShortAnswer(data) bên ngoài
  });

  // Nút Xoá
  $("sa_deleteBtn").addEventListener("click", () => {
    if (confirm("Bạn có chắc muốn xoá câu hỏi này?")) {
      const id = $("sa_id").value;
      if (id) {
        // Gọi xoá từ database (tuỳ hệ thống bạn)
        console.log("Xoá câu hỏi ID:", id);
        // Gợi ý: gọi hàm deleteShortAnswer(id)
      } else {
        alert("Không có câu hỏi để xoá.");
      }
    }
  });
});
</script>
