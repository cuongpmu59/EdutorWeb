<?php require 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý câu hỏi trắc nghiệm</title>
    <link rel="stylesheet" href="css/styles_question.css">
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" defer></script>
    <script src="js/question_script.js" defer></script>
</head>
<body>
    <h1>Quản lý câu hỏi trắc nghiệm</h1>

    <div class="container">
        <!-- Cột trái: Form nhập liệu -->
        <div class="left-column">
            <form id="questionForm" enctype="multipart/form-data">
                <input type="hidden" id="question_id" name="id">

                <label for="question">Câu hỏi (hỗ trợ MathJax):</label>
                <textarea id="question" name="question" rows="4" placeholder="Nhập câu hỏi..."></textarea>
                <div id="preview"></div>

                <label for="image">Ảnh minh hoạ (nếu có):</label>
                <input type="file" id="image" name="image" accept="image/*">
                <img id="imagePreview" style="max-height: 100px; margin-top: 5px; display: none;" alt="Ảnh xem trước" />

                <div class="answer-row"><label>A:</label><input type="text" id="answer1" name="answer1"></div>
                <div class="answer-row"><label>B:</label><input type="text" id="answer2" name="answer2"></div>
                <div class="answer-row"><label>C:</label><input type="text" id="answer3" name="answer3"></div>
                <div class="answer-row"><label>D:</label><input type="text" id="answer4" name="answer4"></div>

                <label>Đáp án đúng:</label>
                <select id="correct_answer" name="correct_answer">
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>

                <!-- Các nút thao tác -->
                <div class="form-buttons" style="margin-top: 20px;">
                    <button type="button" id="saveBtn">Lưu</button>
                    <button type="button" id="updateBtn">Cập nhật</button>
                    <button type="button" id="deleteBtn">Xóa</button>
                    <button type="reset">Làm mới</button>
                </div>
            </form>
        </div>

      
        <!-- Cột phải: Bảng câu hỏi -->
        <div class="right-column">
            <button type="button" id="toggleTableBtn" style="margin-bottom: 10px;">Ẩn bảng câu hỏi</button>
        <iframe id="questionIframe" src="get_question.php" width="100%" height="500" style="border: 1px solid #ccc;"></iframe>
        </div>




    </div>

</body>
</html>
