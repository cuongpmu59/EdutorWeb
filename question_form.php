<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

</head>
<body>
  <h2>Nhập câu hỏi</h2>
  <label><input type="checkbox" id="togglePreview" checked onchange="togglePreview()"> Hiện xem trước công thức</label><br>

  <form id="questionForm" enctype="multipart/form-data">
    <div class="question-container">
      <div class="form-left">
        <input type="hidden" name="id" id="question_id">

        <label for="topic">Chủ đề:</label>
        <input type="text" name="topic" id="topic" required>

        <label for="question">Câu hỏi:</label>
        <textarea name="question" id="question" rows="3" required oninput="renderPreview('question')"></textarea>
        <div id="preview_question" class="latex-preview"></div>

        <label for="answer1">Đáp án A:</label>
        <input type="text" name="answer1" id="answer1" required oninput="renderPreview('answer1')">
        <div id="preview_answer1" class="latex-preview"></div>

        <label for="answer2">Đáp án B:</label>
        <input type="text" name="answer2" id="answer2" required oninput="renderPreview('answer2')">
        <div id="preview_answer2" class="latex-preview"></div>

        <label for="answer3">Đáp án C:</label>
        <input type="text" name="answer3" id="answer3" required oninput="renderPreview('answer3')">
        <div id="preview_answer3" class="latex-preview"></div>

        <label for="answer4">Đáp án D:</label>
        <input type="text" name="answer4" id="answer4" required oninput="renderPreview('answer4')">
        <div id="preview_answer4" class="latex-preview"></div>

        <label for="correct_answer">Đáp án đúng:</label>
        <select name="correct_answer" id="correct_answer" required>
          <option value="">--Chọn--</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>

        <label for="image">Ảnh minh họa:</label>
        <input type="file" name="image" id="image">
        <small id="imageFileName" class="text-muted"></small>

        <img id="imagePreview">
        <input type="hidden" name="image_url" id="image_url">
        <label id="deleteImageLabel" style="display:none;">
          <input type="checkbox" id="delete_image"> Xóa ảnh minh họa
        </label>
      </div>

      <div class="form-right">
        <button type="button" onclick="saveQuestion()">Lưu</button>
        <button type="button" class="delete-btn" onclick="deleteQuestion()">Xoá</button>
        <button type="button" class="search-btn" onclick="searchQuestion()">Tìm kiếm</button>
        <button type="reset" class="reset-btn" onclick="resetPreview()">Làm mới</button>
        

      </div>
    </div>

    <label style="margin-top:15px; display:inline-block;">
      <input type="checkbox" id="toggleFullPreview" checked onchange="toggleFullPreview()"> Hiện xem trước toàn bộ
    </label>
    <div id="fullPreview" class="full-preview"></div>
  </form>

  <h3 style="display: flex; justify-content: space-between; align-items: center;">
  <span>Danh sách câu hỏi</span>
  <span>
    <button onclick="document.getElementById('importFile').click()">📥 Nhập Excel</button>
    <input type="file" id="importFile" style="display:none" accept=".xlsx,.xls" onchange="importExcel(this.files[0])">
    <button onclick="exportToExcel()">📤 Xuất Excel</button>
  </span>
  </h3>
  <iframe id="questionIframe" src="get_question.php" width="100%" height="400"></iframe>

  <!-- Modal tìm kiếm kết quả -->
  <div id="searchModal" class="modal" style="display: none;">
    <div class="modal-content">
      <span class="close" onclick="closeSearchModal()">&times;</span>
      <h3>Kết quả tìm kiếm</h3>
      <table id="searchResultsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Chủ đề</th>
            <th>Câu hỏi</th>
            <th>Đáp án đúng</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- JavaScript chính -->
  <script src="js/question_script.js"></script>
</body>
</html>
