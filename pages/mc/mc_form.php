<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/mc/mc_form.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
  <div class="form-container">
    <!-- CỘT TRÁI: NHẬP CÂU HỎI VÀ ĐÁP ÁN -->
    <div class="column-left">
      <h2>
        Nhập câu hỏi trắc nghiệm
        <button type="button" class="eye-icon" data-preview="full">
          <i class="fas fa-eye"></i>
        </button>
      </h2>

      <form id="questionForm">
        <div class="form-group">
          <label for="topic">Chủ đề:</label>
          <input type="text" id="topic" name="topic" required>
        </div>

        <div class="form-group">
          <label for="question">Câu hỏi:</label>
          <textarea id="question" name="question" rows="3" required></textarea>
        </div>

        <div class="answer-group">
          <label for="answerA">A:</label>
          <input type="text" id="answerA" name="answerA" required>
          <button type="button" class="eye-icon" data-preview="A"><i class="fas fa-eye"></i></button>
        </div>

        <div class="answer-group">
          <label for="answerB">B:</label>
          <input type="text" id="answerB" name="answerB" required>
          <button type="button" class="eye-icon" data-preview="B"><i class="fas fa-eye"></i></button>
        </div>

        <div class="answer-group">
          <label for="answerC">C:</label>
          <input type="text" id="answerC" name="answerC" required>
          <button type="button" class="eye-icon" data-preview="C"><i class="fas fa-eye"></i></button>
        </div>

        <div class="answer-group">
          <label for="answerD">D:</label>
          <input type="text" id="answerD" name="answerD" required>
          <button type="button" class="eye-icon" data-preview="D"><i class="fas fa-eye"></i></button>
        </div>

        <div class="form-group">
          <label for="correct">Đáp án đúng:</label>
          <select id="correct" name="correct" required>
            <option value="">--Chọn--</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
          </select>
        </div>
      </form>

      <!-- KHUNG XEM TRƯỚC CÔNG THỨC -->
      <div id="previewBox" class="preview-box"></div>
    </div>

    <!-- CỘT PHẢI: ẢNH MINH HỌA + NÚT -->
    <div class="column-right">
      <div class="image-section">
        <h3>Ảnh minh hoạ</h3>
        <input type="file" id="imageInput" accept="image/*" hidden>
        <button type="button" id="selectImageBtn"><i class="fas fa-upload"></i> Chọn ảnh</button>
        <button type="button" id="deleteImageBtn"><i class="fas fa-trash"></i> Xoá ảnh</button>
        <div class="image-preview" id="imagePreview"></div>
      </div>

      <div class="action-buttons">
        <button type="button" id="saveBtn" class="btn-primary"><i class="fas fa-save"></i> Lưu</button>
        <button type="button" id="resetBtn"><i class="fas fa-sync-alt"></i> Làm lại</button>
        <button type="button" id="deleteBtn"><i class="fas fa-trash-alt"></i> Xoá</button>
        <a href="mc_table.php" class="btn-link"><i class="fas fa-table"></i> Xem bảng</a>
      </div>
    </div>
  </div>

  <script src="/js/mc/mc_form.js"></script>
</body>
</html>
