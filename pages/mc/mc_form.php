<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="css/modules/form.css">
</head>
<body>
  <div class="form-layout">
    <!-- CỘT TRÁI: FORM NHẬP -->
    <div class="form-left">
      <h2>Nhập câu hỏi</h2>
      <label>Chủ đề:</label>
      <input type="text" name="topic" placeholder="Nhập chủ đề..."><br>

      <label>Câu hỏi:</label>
      <textarea name="question" rows="4" placeholder="Nhập câu hỏi..."></textarea><br>

      <label>Đáp án A:</label>
      <input type="text" name="answer_a" placeholder="Đáp án A"><br>

      <label>Đáp án B:</label>
      <input type="text" name="answer_b" placeholder="Đáp án B"><br>

      <label>Đáp án C:</label>
      <input type="text" name="answer_c" placeholder="Đáp án C"><br>

      <label>Đáp án D:</label>
      <input type="text" name="answer_d" placeholder="Đáp án D"><br>

      <label>Đáp án đúng:</label>
      <select name="correct">
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
      </select>
    </div>

    <!-- CỘT PHẢI: HÌNH ẢNH + NÚT -->
    <div class="form-right">
      <!-- ẢNH MINH HOẠ -->
      <div class="image-preview" id="imageBox">
        <img id="previewImage" src="" alt="Ảnh minh hoạ sẽ hiển thị ở đây">
      </div>

      <!-- NÚT TẢI ẢNH + XOÁ ẢNH -->
      <div class="row-buttons">
        <input type="file" id="imageInput" hidden>
        <button onclick="document.getElementById('imageInput').click()">📷 Tải ảnh</button>
        <button onclick="deleteImage()">❌ Xoá ảnh</button>
      </div>

      <!-- CÁC NÚT CHÍNH -->
      <div class="column-buttons">
        <button onclick="saveQuestion()">💾 Lưu câu hỏi</button>
        <button onclick="resetForm()">🔄 Làm lại</button>
        <button onclick="deleteQuestion()">🗑️ Xoá câu hỏi</button>
        <button onclick="openTable()">📋 Xem bảng</button>
      </div>
    </div>
  </div>

  <script>
    function deleteImage() {
      document.getElementById('previewImage').src = '';
    }

    function saveQuestion() {
      alert('Chức năng Lưu đang phát triển...');
    }

    function resetForm() {
      document.querySelectorAll('.form-left input, .form-left textarea, .form-left select')
        .forEach(el => el.value = '');
      deleteImage();
    }

    function deleteQuestion() {
      if (confirm("Bạn có chắc chắn muốn xoá câu hỏi này?")) {
        alert('Đã xoá!');
      }
    }

    function openTable() {
      alert('Chuyển đến bảng câu hỏi...');
    }
  </script>
</body>
</html>
