<?php
// question_form.php
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý câu hỏi trắc nghiệm</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/styles_question.css" />

    <!-- MathJax CDN -->
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" defer></script>

    <!-- JS xử lý form -->
    <script src="js/question_script.js" defer></script>
</head>
<body>
    <h1>Quản lý câu hỏi trắc nghiệm</h1>
    <div class="container">
        <!-- Form nhập câu hỏi -->
        <div class="left-column">
            <form id="questionForm" enctype="multipart/form-data">
                <label for="question">Câu hỏi (hỗ trợ MathJax):</label>
                <textarea id="question" name="question" rows="4" placeholder="Nhập câu hỏi..."></textarea>
                <div id="preview"></div>

                <label for="image">Ảnh (tuỳ chọn):</label>
                <input type="file" id="image" name="image" accept="image/*" />
                <img id="imagePreview" alt="Ảnh xem trước" />

                <div class="answer-row">
                    <label for="answer1">Đáp án A:</label>
                    <input type="text" id="answer1" name="answer1" placeholder="Đáp án A" />
                </div>
                <div class="answer-row">
                    <label for="answer2">Đáp án B:</label>
                    <input type="text" id="answer2" name="answer2" placeholder="Đáp án B" />
                </div>
                <div class="answer-row">
                    <label for="answer3">Đáp án C:</label>
                    <input type="text" id="answer3" name="answer3" placeholder="Đáp án C" />
                </div>
                <div class="answer-row">
                    <label for="answer4">Đáp án D:</label>
                    <input type="text" id="answer4" name="answer4" placeholder="Đáp án D" />
                </div>

                <label for="correct_answer">Đáp án đúng:</label>
                <select id="correct_answer" name="correct_answer" >
                    <option value="answer1">A</option>
                    <option value="answer2">B</option>
                    <option value="answer3">C</option>
                    <option value="answer4">D</option>
                </select>

                <div class="right-column" style="margin-top: 20px;">
                    <button type="submit">Thêm câu hỏi</button>
                    <button type="button" id="updateBtn">Sửa câu hỏi</button>
                    <button type="button" onclick="deleteSelected()">Xóa câu hỏi</button>
                </div>
            </form>
        </div>

        <!-- Bảng câu hỏi trong iframe -->
        <div class="right-column" style="flex: 2;">
            <iframe id="questionTable" src="get_question.php"></iframe>
        </div>
    </div>
</body>
</html>
