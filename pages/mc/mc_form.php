<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/form.css" />
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
  <div class="form-container">
    <!-- 🔰 Tiêu đề có icon toggle -->
    <h2 style="cursor: pointer;" onclick="toggleFullPreview()">
      📝 Nhập câu hỏi trắc nghiệm nhiều lựa chọn
    </h2>

    <!-- ✅ Khung xem trước toàn bộ (ẩn mặc định) -->
    <div id="fullPreviewBox" style="display: none; margin-bottom: 20px; border: 1px solid #ccc; padding: 15px; border-radius: 10px; background: #f9f9f9;">
      <h3>Xem trước toàn bộ câu hỏi</h3>
      <div id="preview-full-content"></div>
    </div>

    <!-- ⚙️ Form nhập liệu -->
    <form id="mcForm" enctype="multipart/form-data">
      <input type="hidden" id="mc_id" name="mc_id" />
      <div class="form-layout">
        <!-- Cột trái -->
        <div class="form-left">
          <label>Câu hỏi:</label>
          <textarea id="mc_question" name="mc_question" rows="3" required></textarea>

          <label>Chủ đề:</label>
          <input type="text" id="mc_topic" name="mc_topic" />

          <label>Ảnh minh hoạ:</label>
          <input type="file" id="mc_image" name="mc_image" accept="image/*" />
          <div id="image-preview" style="margin-top: 10px;"></div>

          <label>Đáp án A:</label>
          <input type="text" id="mc_a" name="mc_a" required />

          <label>Đáp án B:</label>
          <input type="text" id="mc_b" name="mc_b" required />

          <label>Đáp án C:</label>
          <input type="text" id="mc_c" name="mc_c" required />

          <label>Đáp án D:</label>
          <input type="text" id="mc_d" name="mc_d" required />

          <label>Đáp án đúng:</label>
          <select id="mc_answer" name="mc_answer" required>
            <option value="">--Chọn--</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
          </select>
        </div>

        <!-- Cột phải -->
        <div class="form-right">
          <button type="button" onclick="saveQuestion()">💾 Lưu</button>
          <button type="reset">🔄 Làm mới</button>
          <button type="button" onclick="deleteQuestion()">🗑️ Xoá</button>
          <button type="button" onclick="exportToPDF()">📄 PDF</button>
          <button type="button" onclick="updateFullPreview()">👁️ Xem trước toàn bộ</button>
        </div>
      </div>
    </form>

    <!-- 🧾 iframe hiển thị danh sách câu hỏi -->
    <iframe id="questionListFrame" src="mc_list.php" style="width: 100%; height: 400px; border: none; margin-top: 30px;"></iframe>
  </div>

  <script>
    // Hiển thị / Ẩn toàn bộ xem trước
    function toggleFullPreview() {
      const box = document.getElementById("fullPreviewBox");
      box.style.display = box.style.display === "none" ? "block" : "none";
      if (box.style.display === "block") {
        updateFullPreview();
      }
    }

    // Cập nhật nội dung xem trước toàn bộ
    function updateFullPreview() {
      const question = document.getElementById("mc_question").value;
      const topic = document.getElementById("mc_topic").value;
      const a = document.getElementById("mc_a").value;
      const b = document.getElementById("mc_b").value;
      const c = document.getElementById("mc_c").value;
      const d = document.getElementById("mc_d").value;
      const answer = document.getElementById("mc_answer").value;
      const imgInput = document.getElementById("mc_image");
      const previewBox = document.getElementById("preview-full-content");

      let html = `<p><strong>Chủ đề:</strong> ${topic}</p>`;
      html += `<p><strong>Câu hỏi:</strong> ${question}</p>`;

      if (imgInput.files && imgInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          html += `<p><strong>Ảnh minh hoạ:</strong><br><img src="${e.target.result}" style="max-width: 100%; height: auto;" /></p>`;
          html += `<p><strong>Đáp án:</strong><br>
                    A. ${a}<br>
                    B. ${b}<br>
                    C. ${c}<br>
                    D. ${d}<br>
                    <strong>Đáp án đúng:</strong> ${answer}</p>`;
          previewBox.innerHTML = html;
          MathJax.typesetPromise();
        };
        reader.readAsDataURL(imgInput.files[0]);
      } else {
        html += `<p><strong>Đáp án:</strong><br>
                  A. ${a}<br>
                  B. ${b}<br>
                  C. ${c}<br>
                  D. ${d}<br>
                  <strong>Đáp án đúng:</strong> ${answer}</p>`;
        previewBox.innerHTML = html;
        MathJax.typesetPromise();
      }
    }

    // Placeholder hàm xử lý lưu
    function saveQuestion() {
      alert("📝 Đã nhấn Lưu. (Hàm xử lý chưa được triển khai ở đây)");
    }

    function deleteQuestion() {
      alert("🗑️ Xoá câu hỏi. (Hàm xử lý chưa được triển khai ở đây)");
    }

    function exportToPDF() {
      alert("📄 Xuất PDF. (Hàm xử lý chưa được triển khai ở đây)");
    }
  </script>
</body>
</html>
