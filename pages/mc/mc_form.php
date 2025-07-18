<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="/css/main_ui.css">
  <link rel="stylesheet" href="/css/form/form_ui.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body>
  <div class="form-layout">
    <!-- ===== CỘT TRÁI: FORM NHẬP ===== -->
    <div class="form-left">
      <h2>Nhập câu hỏi trắc nghiệm</h2>
      <form id="question-form" enctype="multipart/form-data">
        <input type="hidden" id="question-id" name="id">

        <div class="form-group">
          <label for="topic">Chủ đề:</label>
          <input type="text" id="topic" name="topic" placeholder="VD: Đại số, Hình học">
        </div>

        <div class="form-group">
          <label for="question">Câu hỏi:</label>
          <textarea id="question" name="question" rows="3" placeholder="Nhập nội dung câu hỏi (có thể dùng LaTeX)"></textarea>
          <button type="button" class="preview-toggle" data-target="question-preview" title="Xem trước câu hỏi">&#128065;</button>
          <div id="question-preview" class="preview-box" style="display: none;"></div>
        </div>

        <div class="form-group">
          <label>Đáp án:</label>
          <?php foreach (['A', 'B', 'C', 'D'] as $opt): ?>
            <div class="answer-row">
              <label for="answer-<?= $opt ?>"><?= $opt ?>:</label>
              <input type="text" id="answer-<?= $opt ?>" name="answer_<?= $opt ?>" placeholder="Nhập đáp án <?= $opt ?>">
              <button type="button" class="preview-toggle" data-target="preview-<?= $opt ?>" title="Xem trước">&#128065;</button>
              <div id="preview-<?= $opt ?>" class="preview-box" style="display: none;"></div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="form-group">
          <label for="correct_answer">Đáp án đúng:</label>
          <select id="correct_answer" name="correct_answer">
            <option value="">-- Chọn đáp án đúng --</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
          </select>
        </div>

        <div class="button-group">
          <button type="submit" id="save-btn">💾 Lưu</button>
          <button type="button" id="reset-btn">🧹 Làm mới</button>
          <button type="button" id="delete-btn" style="display: none;">🗑️ Xoá</button>
          <button type="button" id="export-pdf-btn">📄 Xuất PDF</button>
        </div>
      </form>
    </div>

    <!-- ===== CỘT PHẢI: ẢNH MINH HOẠ ===== -->
    <div class="form-right">
      <div class="tab-header">
        <button class="tab-btn active" data-tab="image-tab">🖼️ Ảnh minh hoạ</button>
        <button class="tab-btn" data-tab="preview-tab">👁️ Xem trước toàn bộ</button>
      </div>

      <div class="tab-content image-tab active">
        <input type="file" id="image" name="image" accept="image/*">
        <div id="image-preview" class="image-preview"></div>
        <button type="button" id="remove-image-btn" style="display: none;">❌ Xoá ảnh</button>
      </div>

      <div class="tab-content preview-tab">
        <h4>Xem trước toàn bộ:</h4>
        <div id="full-preview" class="preview-box"></div>
      </div>
    </div>
  </div>

  <!-- ===== DANH SÁCH CÂU HỎI (IFRAME) ===== -->
  <iframe id="question-table" src="get_question.php" style="width:100%; height:600px; border:none; margin-top:20px;"></iframe>

  <script src="/js/question_script.js"></script>
</body>
</html>
