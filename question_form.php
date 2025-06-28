<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi trắc nghiệm</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
  <div class="header-bar">
    <h2>Quản lý câu hỏi trắc nghiệm</h2>
    <label class="switch">
      <input type="checkbox" id="toggleDarkMode">
      <span class="slider round"></span>
    </label>
  </div>

  <div class="question-container">
    <form id="questionForm">
      <input type="hidden" id="question_id" name="question_id">
      <input type="hidden" id="image_url" name="image_url">

      <label for="topic">Chủ đề:</label>
      <input type="text" id="topic" name="topic" required>

      <label for="question">Câu hỏi:</label>
      <textarea id="question" name="question" rows="2" required></textarea>
      <div id="preview_question" class="preview-field"></div>

      <label for="answer1">Đáp án A:</label>
      <textarea id="answer1" name="answer1" rows="2" required></textarea>
      <div id="preview_answer1" class="preview-field"></div>

      <label for="answer2">Đáp án B:</label>
      <textarea id="answer2" name="answer2" rows="2" required></textarea>
      <div id="preview_answer2" class="preview-field"></div>

      <label for="answer3">Đáp án C:</label>
      <textarea id="answer3" name="answer3" rows="2" required></textarea>
      <div id="preview_answer3" class="preview-field"></div>

      <label for="answer4">Đáp án D:</label>
      <textarea id="answer4" name="answer4" rows="2" required></textarea>
      <div id="preview_answer4" class="preview-field"></div>

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
      <label id="deleteImageLabel" style="display:none">
        <input type="checkbox" id="delete_image" name="delete_image"> Xóa ảnh hiện tại
      </label>
      <img id="previewImage" src="" class="preview-thumb" style="display:none" onclick="zoomImage(this)">

      <div class="button-group">
        <button type="button" onclick="addQuestion()">Thêm</button>
        <button type="button" onclick="updateQuestion()">Sửa</button>
        <button type="button" onclick="deleteQuestion()">Xoá</button>
        <button type="reset">Làm mới</button>
        <button type="button" onclick="openSearchModal()">Tìm kiếm</button>
        <button type="button" onclick="document.getElementById('importCSV').click()">Nhập CSV</button>
        <button onclick="window.open('export_question.php')" type="button">📤 Xuất Excel</button>
      </div>

      <input type="file" id="importCSV" accept=".csv" style="display:none">
    </form>

    <form action="generate_exam_pdf.php" method="get" target="_blank" style="margin-top: 10px;">
      <label>Chọn chủ đề xuất đề thi:</label>
      <select name="topic" id="topicExport">
        <option value="">-- Tất cả --</option>
        <option value="Đại số">Đại số</option>
        <option value="Hình học">Hình học</option>
        <!-- thêm các chủ đề khác nếu cần -->
      </select>
      <button type="submit">📄 Xuất đề thi PDF</button>
    </form>

    <hr>
    <label><input type="checkbox" id="togglePreview" checked> Hiện xem trước toàn bộ</label>

    <div id="previewBox" class="preview-box">
      <h3>Xem trước toàn bộ nội dung</h3>
      <div><strong>ID:</strong> <span id="pv_id"></span></div>
      <div><strong>Chủ đề:</strong> <span id="pv_topic"></span></div>
      <div><strong>Câu hỏi:</strong> <span id="pv_question"></span></div>
      <div><strong>Đáp án A:</strong> <span id="pv_a"></span></div>
      <div><strong>Đáp án B:</strong> <span id="pv_b"></span></div>
      <div><strong>Đáp án C:</strong> <span id="pv_c"></span></div>
      <div><strong>Đáp án D:</strong> <span id="pv_d"></span></div>
      <div><strong>Đáp án đúng:</strong> <span id="pv_correct"></span></div>
      <div><strong>Ảnh:</strong><br><img id="pv_image" src="" style="max-width: 200px; margin-top: 5px; display: none;"></div>
    </div>
  </div>

  <hr>
  <h3>Danh sách câu hỏi</h3>
  <iframe id="questionIframe" src="get_question.php" width="100%" height="400" style="border: 1px solid #ccc;"></iframe>

  <!-- Modal ảnh -->
  <div id="imageModal" class="modal" onclick="this.style.display='none'">
    <img class="modal-content" id="modalImage">
  </div>

  <!-- Modal tìm kiếm -->
  <div id="searchModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeSearchModal()">&times;</span>
      <h3>Tìm kiếm câu hỏi</h3>
      <input type="text" id="searchKeyword" placeholder="Nhập từ khóa...">
      <button onclick="searchQuestion()">Tìm</button>
      <button class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#xlsxModal">
        📥 Nhập Excel (.xlsx)
      </button>

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
      previewFull, openSearchModal, closeSearchModal, searchQuestion,
      zoomImage, importCSV
    } from './js/question_script.js';

    window.addQuestion = addQuestion;
    window.updateQuestion = updateQuestion;
    window.deleteQuestion = deleteQuestion;
    window.previewFull = previewFull;
    window.openSearchModal = openSearchModal;
    window.closeSearchModal = closeSearchModal;
    window.searchQuestion = searchQuestion;
    window.zoomImage = zoomImage;
    window.importCSV = importCSV;

    document.getElementById("importCSV").addEventListener("change", importCSV);

    // Nhận dữ liệu từ iframe và điền vào form
    window.addEventListener("message", function (event) {
      if (!event.data || event.data.type !== "fillForm") return;

      const q = event.data.data;

      document.getElementById("question_id").value = q.id;
      document.getElementById("topic").value = q.topic;
      document.getElementById("question").value = q.question;
      document.getElementById("answer1").value = q.answer1;
      document.getElementById("answer2").value = q.answer2;
      document.getElementById("answer3").value = q.answer3;
      document.getElementById("answer4").value = q.answer4;
      document.getElementById("correct_answer").value = q.correct_answer;
      document.getElementById("image_url").value = q.image;

      const previewImg = document.getElementById("previewImage");
      const deleteLabel = document.getElementById("deleteImageLabel");
      if (q.image) {
        previewImg.src = q.image;
        previewImg.style.display = "block";
        deleteLabel.style.display = "inline-block";
      } else {
        previewImg.style.display = "none";
        deleteLabel.style.display = "none";
      }

      previewFull();
    });
  </script>

  <!-- Modal Nhập Excel -->
<div class="modal fade" id="xlsxModal" tabindex="-1" aria-labelledby="xlsxModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="xlsxUploadForm" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title" id="xlsxModalLabel">📥 Nhập câu hỏi từ Excel (.xlsx)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Chọn file Excel (.xlsx):</label>
        <input type="file" name="xlsx_file" accept=".xlsx" class="form-control" required>
        <div class="mt-2">
          <a href="template.xlsx" download class="btn btn-outline-secondary btn-sm">📄 Tải file mẫu Excel</a>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Tải lên</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
