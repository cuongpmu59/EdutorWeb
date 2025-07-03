<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Nhập câu hỏi trắc nghiệm</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/styles_question.css" />
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="light-mode">
  <div class="container">
    <h2>📘 Nhập câu hỏi trắc nghiệm</h2>
    <form id="questionForm" enctype="multipart/form-data">
      <input type="hidden" name="id" id="question_id" />

      <div class="form-group">
        <label>Chủ đề:</label>
        <input type="text" name="topic" id="topic" required />
      </div>

      <div class="form-group">
        <label>Câu hỏi:</label>
        <textarea name="question" id="question" rows="3" required></textarea>
      </div>

      <div class="form-group">
        <label>Đáp án A:</label>
        <input type="text" name="answer1" id="answer1" required />
      </div>

      <div class="form-group">
        <label>Đáp án B:</label>
        <input type="text" name="answer2" id="answer2" required />
      </div>

      <div class="form-group">
        <label>Đáp án C:</label>
        <input type="text" name="answer3" id="answer3" required />
      </div>

      <div class="form-group">
        <label>Đáp án D:</label>
        <input type="text" name="answer4" id="answer4" required />
      </div>

      <div class="form-group">
        <label>Đáp án đúng:</label>
        <select name="correct_answer" id="correct_answer" required>
          <option value="">-- Chọn --</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>
      </div>

      <div class="form-group">
        <label>Ảnh minh họa:</label><br />
        <input type="hidden" name="image_url" id="image_url" />
        <input type="file" id="image_input" accept="image/*" style="display:none;" />
        <button type="button" id="select_image">📷 Chọn ảnh</button>
        <button type="button" id="delete_image" style="display:none;">🗑️ Xoá ảnh</button>
        <br />
        <img id="preview_image" src="" style="max-width:150px; display:none; margin-top:10px; border:1px solid #ccc;" />
      </div>

      <div class="form-group checkbox-group">
        <label><input type="checkbox" id="toggle_preview_question" checked /> Hiện câu hỏi</label>
        <label><input type="checkbox" id="toggle_preview_answers" checked /> Hiện đáp án</label>
        <label><input type="checkbox" id="toggle_preview_all" checked /> Xem trước toàn bộ</label>
      </div>

      <div class="form-group button-group">
        <button type="submit" id="saveBtn">💾 Lưu</button>
        <button type="reset" id="resetBtn">🧹 Làm mới</button>
        <button type="button" id="deleteBtn">❌ Xoá</button>
        <button type="button" id="exportPdfBtn">📄 Xuất đề thi PDF</button>
      </div>
    </form>

    <div id="preview_area" class="preview-box"></div>

    <iframe src="get_question.php" id="questionIframe" style="width:100%; height:400px; border:1px solid #ccc; margin-top:20px;"></iframe>
  </div>

  <script src="js/question_script.js"></script>
</body>
</html>
