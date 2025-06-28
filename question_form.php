<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi trắc nghiệm</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  
  <!-- CSS & MathJax -->
  <link rel="stylesheet" href="css/styles_question.css">
  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>

<body>
  <div class="header-bar d-flex justify-content-between align-items-center p-2 bg-light">
    <h2 class="m-0">Quản lý câu hỏi trắc nghiệm</h2>
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" id="toggleDarkMode">
    </div>
  </div>

  <div class="container my-4">
    <form id="questionForm" enctype="multipart/form-data">
      <input type="hidden" id="question_id" name="question_id">
      <input type="hidden" id="image_url" name="image_url">

      <!-- Chủ đề & câu hỏi -->
      <div class="mb-3">
        <label for="topic" class="form-label">Chủ đề:</label>
        <input type="text" class="form-control" id="topic" name="topic" required>
      </div>

      <div class="mb-3">
        <label for="question" class="form-label">Câu hỏi:</label>
        <textarea class="form-control" id="question" name="question" rows="2" required></textarea>
        <div id="preview_question" class="preview-field"></div>
      </div>

      <!-- Các đáp án -->
      <?php
      $answers = ['A', 'B', 'C', 'D'];
      foreach ($answers as $i => $label) {
        echo <<<HTML
        <div class="mb-3">
          <label for="answer{$i+1}" class="form-label">Đáp án {$label}:</label>
          <textarea class="form-control" id="answer{$i+1}" name="answer{$i+1}" rows="2" required></textarea>
          <div id="preview_answer{$i+1}" class="preview-field"></div>
        </div>
        HTML;
      }
      ?>

      <!-- Đáp án đúng -->
      <div class="mb-3">
        <label for="correct_answer" class="form-label">Đáp án đúng:</label>
        <select id="correct_answer" name="correct_answer" class="form-select" required>
          <option value="">--Chọn--</option>
          <?php foreach ($answers as $a) echo "<option value='$a'>$a</option>"; ?>
        </select>
      </div>

      <!-- Ảnh minh hoạ -->
      <div class="mb-3">
        <label for="image" class="form-label">Ảnh minh họa:</label>
        <input class="form-control" type="file" id="image" name="image" accept="image/*">
        <label id="deleteImageLabel" style="display:none">
          <input type="checkbox" id="delete_image" name="delete_image"> Xóa ảnh hiện tại
        </label>
        <img id="previewImage" src="" class="preview-thumb mt-2" style="display:none; max-width: 150px;" onclick="showImageModal(this.src)">
      </div>

      <!-- Nút thao tác -->
      <div class="mb-4 d-flex flex-wrap gap-2">
        <button type="button" onclick="addQuestion()" class="btn btn-success">Thêm</button>
        <button type="button" onclick="updateQuestion()" class="btn btn-warning">Sửa</button>
        <button type="button" onclick="deleteQuestion()" class="btn btn-danger">Xoá</button>
        <button type="reset" class="btn btn-secondary">Làm mới</button>
        <button type="button" onclick="openSearchModal()" class="btn btn-info">Tìm kiếm</button>
        <button type="button" onclick="document.getElementById('importCSV').click()" class="btn btn-outline-dark">Nhập CSV</button>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#xlsxModal">📤 Nhập Excel</button>
        <button type="button" onclick="window.open('export_question.php')" class="btn btn-outline-success">📤 Xuất Excel</button>
      </div>

      <input type="file" id="importCSV" accept=".csv" style="display:none">
    </form>

    <!-- Xuất PDF -->
    <form action="generate_exam_pdf.php" method="get" target="_blank" class="my-3">
      <label>Chọn chủ đề xuất đề thi:</label>
      <select name="topic" id="topicExport" class="form-select d-inline w-auto mx-2">
        <option value="">-- Tất cả --</option>
        <option value="Đại số">Đại số</option>
        <option value="Hình học">Hình học</option>
      </select>
      <button type="submit" class="btn btn-outline-dark">📄 Xuất đề thi PDF</button>
    </form>

    <!-- Xem trước -->
    <hr>
    <div class="form-check mb-2">
      <input type="checkbox" class="form-check-input" id="togglePreview" checked>
      <label class="form-check-label" for="togglePreview">Hiện xem trước toàn bộ</label>
    </div>

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
      <div><strong>Ảnh:</strong><br><img id="pv_image" src="" style="max-width:200px; display:none;"></div>
    </div>

    <hr>
    <h3>Danh sách câu hỏi</h3>
    <iframe id="questionIframe" src="get_question.php" width="100%" height="400" style="border:1px solid #ccc;"></iframe>
  </div>

  <!-- 🌌 Modal hiển thị ảnh phóng to -->
<div id="imageModal" style="
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.85);
    z-index: 1000;
    justify-content: center;
    align-items: center;
">
  <img id="modalImage" style="
    max-width: 90%;
    max-height: 90%;
    border-radius: 8px;
    box-shadow: 0 0 10px #fff;
  ">
</div>

<script>
  function showImageModal(src) {
    const modal = document.getElementById("imageModal");
    const img = document.getElementById("modalImage");
    img.src = src;
    modal.style.display = "flex";
  }

  // Đóng modal khi click ra ngoài ảnh
  document.getElementById("imageModal").addEventListener("click", function () {
    this.style.display = "none";
  });
</script>

  <!-- Modal tìm kiếm & Modal Excel -->
  <?php include 'modals.php'; ?>

  <!-- JavaScript -->
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
  </script>
  <script>
  const darkToggle = document.getElementById("toggleDarkMode");

  // Khôi phục trạng thái dark mode từ localStorage
  if (localStorage.getItem("darkMode") === "on") {
    document.body.classList.add("dark-mode");
    darkToggle.checked = true;
  }

  darkToggle.addEventListener("change", function () {
    if (this.checked) {
      document.body.classList.add("dark-mode");
      localStorage.setItem("darkMode", "on");
    } else {
      document.body.classList.remove("dark-mode");
      localStorage.setItem("darkMode", "off");
    }
  });
</script>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
  <div id="toastMsg" class="toast align-items-center text-white bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastContent">Thành công!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>


</body>
</html>
