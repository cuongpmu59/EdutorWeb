<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/form.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <script src="/js/modules/previewView.js" defer></script>
</head>
<body>
  <h2>📘 Nhập câu hỏi trắc nghiệm</h2>
  <form id="mcForm" method="POST" enctype="multipart/form-data">
    <input type="hidden" id="mc_id" name="mc_id">

    <div class="form-group">
      <label for="mc_topic">Chủ đề</label>
      <input type="text" id="mc_topic" name="mc_topic" required>
    </div>

    <div class="form-group">
      <label for="mc_question">Câu hỏi</label>
      <textarea id="mc_question" name="mc_question" rows="3" required></textarea>
    </div>

    <!-- Đáp án A–D -->
    <div class="form-group-row">
      <label>A:</label>
      <input type="text" name="mc_answer_a" id="mc_answer_a" required>
      <label>B:</label>
      <input type="text" name="mc_answer_b" id="mc_answer_b" required>
      <label>C:</label>
      <input type="text" name="mc_answer_c" id="mc_answer_c" required>
      <label>D:</label>
      <input type="text" name="mc_answer_d" id="mc_answer_d" required>
    </div>

    <!-- Đáp án đúng -->
    <div class="form-group-inline">
      <label for="mc_correct_answer">Đáp án đúng</label>
      <select name="mc_correct_answer" id="mc_correct_answer" required>
        <option value="">-- Chọn --</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
      </select>
    </div>

    <!-- Ảnh minh hoạ -->
    <div class="form-group">
      <label for="mc_image">Ảnh minh hoạ</label>
      <input type="file" id="mc_image" name="mc_image" accept="image/*">
    </div>

    <!-- Buttons -->
    <div class="form-buttons">
      <button type="submit" id="btnSave">💾 Lưu</button>
      <button type="reset" id="btnReset">🔄 Nhập lại</button>
    </div>
  </form>

  <!-- Xem trước -->
  <div class="preview-box">
    <h3>👁️ Xem trước</h3>
    <div id="previewQuestion" class="preview-field"></div>
    <ul>
      <li><strong>A:</strong> <span id="previewA"></span></li>
      <li><strong>B:</strong> <span id="previewB"></span></li>
      <li><strong>C:</strong> <span id="previewC"></span></li>
      <li><strong>D:</strong> <span id="previewD"></span></li>
    </ul>
  </div>
</body>
</html>
