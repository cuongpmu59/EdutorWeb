<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý câu hỏi trắc nghiệm</title>
    <link rel="stylesheet" href="css/question_style.css"> <!-- Nếu có -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async
        src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
    </script>
</head>
<body>
    <h2>Form nhập câu hỏi</h2>
    <form id="questionForm" enctype="multipart/form-data">
        <input type="hidden" id="question_id" name="id">

        <label for="question">Câu hỏi:</label><br>
        <textarea id="question" name="question" rows="3" required></textarea><br>

        <label for="answer1">Đáp án A:</label><br>
        <input type="text" id="answer1" name="answer1" required><br>

        <label for="answer2">Đáp án B:</label><br>
        <input type="text" id="answer2" name="answer2" required><br>

        <label for="answer3">Đáp án C:</label><br>
        <input type="text" id="answer3" name="answer3" required><br>

        <label for="answer4">Đáp án D:</label><br>
        <input type="text" id="answer4" name="answer4" required><br>

        <label for="correct_answer">Đáp án đúng:</label><br>
        <select id="correct_answer" name="correct_answer" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select><br>

        <label for="image">Ảnh minh họa (nếu có):</label><br>
        <input type="file" id="image" name="image" accept="image/*"><br>
        <img id="imagePreview" src="" alt="Preview" style="max-height: 100px; display: none;"><br><br>

        <button type="button" onclick="saveQuestion()">Lưu</button>
        <button type="button" onclick="deleteQuestion()">Xoá</button>
        <button type="reset">Xóa trắng</button>
        <button type="button" onclick="searchQuestion()">Tìm kiếm</button>
    </form>

    <h3>Xem trước công thức (MathJax):</h3>
    <div id="preview" style="border: 1px solid #ccc; padding: 10px;"></div>

    <hr>
    <h3>Danh sách câu hỏi</h3>
    <iframe id="questionIframe" src="get_question.php" width="100%" height="300" style="border:1px solid #ccc;"></iframe>

    <script src="js/question_script.js"></script>
    <script>
        // Xử lý MathJax preview mỗi khi nhập liệu
        document.getElementById("question").addEventListener("input", function () {
            const content = this.value;
            const preview = document.getElementById("preview");
            preview.innerHTML = content;
            if (window.MathJax) MathJax.typesetPromise([preview]);
        });

        // Hiển thị ảnh xem trước
        document.getElementById("image").addEventListener("change", function () {
            const file = this.files[0];
            const preview = document.getElementById("imagePreview");

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
