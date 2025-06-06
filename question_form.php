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
                </div><?php require 'db_connection.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý câu hỏi</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Nếu bạn có style riêng -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>
    <h2>Nhập câu hỏi trắc nghiệm</h2>

    <form id="questionForm" enctype="multipart/form-data">
        <input type="hidden" id="question_id" name="id">

        <label>Câu hỏi:</label><br>
        <textarea id="question" name="question" rows="3" cols="60"></textarea><br><br>

        <label>Đáp án A:</label><br>
        <input type="text" id="answer1" name="answer1"><br><br>

        <label>Đáp án B:</label><br>
        <input type="text" id="answer2" name="answer2"><br><br>

        <label>Đáp án C:</label><br>
        <input type="text" id="answer3" name="answer3"><br><br>

        <label>Đáp án D:</label><br>
        <input type="text" id="answer4" name="answer4"><br><br>

        <label>Đáp án đúng:</label><br>
        <select id="correct_answer" name="correct_answer">
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select><br><br>

        <label>Hình ảnh minh họa (nếu có):</label><br>
        <input type="file" id="image" name="image" accept="image/*"><br>
        <img id="image_preview" src="" style="max-height: 100px; margin-top: 5px; display: none;"><br><br>

        <button type="button" id="saveBtn">Lưu</button>
        <button type="button" id="updateBtn">Cập nhật</button>
        <button type="button" id="deleteBtn">Xóa</button>
        <button type="reset">Làm mới</button>
    </form>

    <hr>

    <h2>Danh sách câu hỏi</h2>
    <iframe id="questionIframe" src="get_question.php" width="100%" height="400" style="border: 1px solid #ccc;"></iframe>

    <script src="js/question_script.js"></script>

    <script>
        // Xem trước hình ảnh khi chọn file mới
        document.getElementById("image").addEventListener("change", function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.getElementById("image_preview");
                    img.src = e.target.result;
                    img.style.display = "inline";
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>

            </form>
        </div>

        <!-- Bảng câu hỏi trong iframe -->
        <div class="right-column" style="flex: 2;">
            <iframe id="questionTable" src="get_question.php"></iframe>
        </div>
    </div>
</body>
</html>
