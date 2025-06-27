<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi trắc nghiệm</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
  <h2>Quản lý câu hỏi trắc nghiệm</h2>
  <div class="question-container">
    <form id="questionForm">
      <input type="hidden" id="question_id" name="question_id">

      <label for="topic">Chủ đề:</label>
      <input type="text" id="topic" name="topic" required>

      <label for="question">Câu hỏi:</label>
      <textarea id="question" name="question" rows="2" required></textarea>

      <label for="answer1">Đáp án A:</label>
      <textarea id="answer1" name="answer1" rows="2" required></textarea>

      <label for="answer2">Đáp án B:</label>
      <textarea id="answer2" name="answer2" rows="2" required></textarea>

      <label for="answer3">Đáp án C:</label>
      <textarea id="answer3" name="answer3" rows="2" required></textarea>

      <label for="answer4">Đáp án D:</label>
      <textarea id="answer4" name="answer4" rows="2" required></textarea>

      <label for="correct_answer">Đáp án đúng:</label>
      <select id="correct_answer" name="correct_answer" required>
        <option value="">--Chọn--</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
      </select>

      <label for="image">Ảnh minh họa:</label>
      <input type="file" id="image" name="image" accept="image/*">
      <img id="previewImage" src="" style="max-width: 150px; display: none; margin-top: 5px;">

      <div class="button-group">
        <button type="button" onclick="addQuestion()">Thêm</button>
        <button type="button" onclick="updateQuestion()">Sửa</button>
        <button type="button" onclick="deleteQuestion()">Xoá</button>
        <button type="reset">Làm mới</button>
        <button type="button" onclick="openSearchModal()">Tìm kiếm</button>
      </div>

      <div style="margin-top: 10px;">
        <button type="button" onclick="previewFull()">Xem trước toàn bộ</button>
      </div>
    </form>

    <hr>
    <label><input type="checkbox" id="togglePreview" checked> Hiện xem trước toàn bộ</label>

    <div id="previewBox" style="display: block; margin-top: 10px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9;">
      <h3>Xem trước toàn bộ nội dung</h3>
      <div><strong>ID:</strong> <span id="pv_id"></span></div>
      <div><strong>Chủ đề:</strong> <span id="pv_topic"></span></div>
      <div><strong>Câu hỏi:</strong> <span id="pv_question"></span></div>
      <div><strong>Đáp án A:</strong> <span id="pv_a"></span></div>
      <div><strong>Đáp án B:</strong> <span id="pv_b"></span></div>
      <div><strong>Đáp án C:</strong> <span id="pv_c"></span></div>
      <div><strong>Đáp án D:</strong> <span id="pv_d"></span></div>
      <div><strong>Đáp án đúng:</strong> <span id="pv_correct"></span></div>
      <div><strong>Ảnh:</strong><br><img id="pv_image" src="" style="max-width: 200px; margin-top: 5px;" /></div>
    </div>
  </div>

  <hr>
  <h3>Danh sách câu hỏi</h3>
  <iframe id="questionIframe" src="get_question.php" width="100%" height="300" style="border: 1px solid #ccc;"></iframe>

  <!-- Modal tìm kiếm -->
  <div id="searchModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeSearchModal()">&times;</span>
      <h3>Tìm kiếm câu hỏi</h3>
      <input type="text" id="searchKeyword" placeholder="Nhập từ khóa...">
      <button onclick="searchQuestion()">Tìm</button>

      <table id="searchResultTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Chủ đề</th>
            <th>Câu hỏi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <button onclick="closeSearchModal()">Đóng</button>
    </div>
  </div>

  <script type="module">
  import {
    addQuestion, updateQuestion, deleteQuestion,
    previewFull, openSearchModal, closeSearchModal, searchQuestion
  } from './js/question_script.js';

  window.addQuestion = addQuestion;
  window.updateQuestion = updateQuestion;
  window.deleteQuestion = deleteQuestion;
  window.previewFull = previewFull;
  window.openSearchModal = openSearchModal;
  window.closeSearchModal = closeSearchModal;
  window.searchQuestion = searchQuestion;
</script>
</body>
</html>
