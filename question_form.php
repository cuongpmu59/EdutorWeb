<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Câu hỏi Trắc nghiệm</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async
    src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
  <div class="question-container">
    <h2>Câu hỏi Trắc nghiệm</h2>
    <form id="questionForm" enctype="multipart/form-data">
      <div class="form-left">
        <input type="hidden" id="question_id" name="question_id">

        <label for="topic">Chủ đề:</label>
        <input type="text" id="topic" name="topic" required>

        <label for="question">Câu hỏi:</label>
        <textarea id="question" name="question" oninput="renderPreview('question')" required></textarea>
        <div id="preview_question" class="latex-preview"></div>

        <label for="answer1">Đáp án A:</label>
        <input type="text" id="answer1" name="answer1" oninput="renderPreview('answer1')" required>
        <div id="preview_answer1" class="latex-preview"></div>

        <label for="answer2">Đáp án B:</label>
        <input type="text" id="answer2" name="answer2" oninput="renderPreview('answer2')" required>
        <div id="preview_answer2" class="latex-preview"></div>

        <label for="answer3">Đáp án C:</label>
        <input type="text" id="answer3" name="answer3" oninput="renderPreview('answer3')" required>
        <div id="preview_answer3" class="latex-preview"></div>

        <label for="answer4">Đáp án D:</label>
        <input type="text" id="answer4" name="answer4" oninput="renderPreview('answer4')" required>
        <div id="preview_answer4" class="latex-preview"></div>

        <label for="correct_answer">Đáp án đúng:</label>
        <select id="correct_answer" name="correct_answer" required>
          <option value="">--Chọn--</option>
          <option value="A">A</option>
          <option value="B">B</option>
          <option value="C">C</option>
          <option value="D">D</option>
        </select>

        <label for="image">Ảnh minh hoạ (tùy chọn, &lt; 2MB):</label>
        <input type="file" id="image" name="image" accept="image/*">
        <div id="imageFileName" style="font-size: 14px; color: #555;"></div>

        <input type="hidden" id="image_url" name="image_url">
        <label id="deleteImageLabel" style="display:none">
          <input type="checkbox" id="delete_image" name="delete_image"> Xoá ảnh hiện tại
        </label>
        <img id="imagePreview" class="image-preview" src="" alt="Preview ảnh">

        <div>
          <label><input type="checkbox" id="togglePreview" onclick="togglePreview()"> Hiện xem trước từng phần</label><br>
          <label><input type="checkbox" id="toggleFullPreview" onclick="toggleFullPreview()"> Hiện xem trước toàn bộ</label>
        </div>
        <div id="fullPreview" class="latex-preview"></div>
      </div>

      <div class="form-right">
        <button type="button" onclick="saveQuestion()">Thêm / Cập nhật</button>
        <button type="button" class="delete-btn" onclick="deleteQuestion()">Xoá</button>
        <button type="reset" class="reset-btn" onclick="resetPreview()">Làm mới</button>
        <button type="button" class="search-btn" onclick="searchQuestion()">Tìm kiếm</button>
      </div>
    </form>

    <iframe id="questionIframe" src="get_question.php" class="question-iframe"></iframe>

    <!-- Modal tìm kiếm -->
    <div id="searchModal" class="modal">
      <div class="modal-content">
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
        <button onclick="closeSearchModal()">Đóng</button>
      </div>
    </div>
  </div>

  <script src="js/question_script.js"></script>
</body>
</html>
