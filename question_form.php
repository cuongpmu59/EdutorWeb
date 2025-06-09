<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý câu hỏi</title>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .left-column {
            width: 50%;
            padding: 20px;
            box-sizing: border-box;
            border-right: 1px solid #ccc;
        }

        .right-column {
            width: 50%;
            padding: 20px;
            box-sizing: border-box;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"], select, input[type="file"] {
            width: 100%;
            padding: 6px;
            margin-top: 4px;
        }

        button {
            margin: 6px 4px;
            padding: 8px 16px;
            font-size: 14px;
        }

        #imagePreview {
            margin-top: 10px;
            max-width: 100%;
            max-height: 150px;
        }

        iframe {
            width: 100%;
            height: 400px;
            border: 1px solid #ccc;
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
    </style>

    <!-- MathJax for rendering LaTeX -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async
            src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
    </script>
</head>
<body>
    <div class="left-column">
        <form id="questionForm">
            <input type="hidden" id="id" name="id">

            <label for="question">Câu hỏi:</label>
            <input type="text" id="question" name="question" required>

            <label for="answer1">Đáp án A:</label>
            <input type="text" id="answer1" name="answer1" required>

            <label for="answer2">Đáp án B:</label>
            <input type="text" id="answer2" name="answer2" required>

            <label for="answer3">Đáp án C:</label>
            <input type="text" id="answer3" name="answer3" required>

            <label for="answer4">Đáp án D:</label>
            <input type="text" id="answer4" name="answer4" required>

            <label for="correct_answer">Đáp án đúng:</label>
            <select id="correct_answer" name="correct_answer">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>

            <label for="image">Ảnh minh họa (nếu có):</label>
            <input type="file" id="image" name="image" accept="image/*">
            <img id="imagePreview" src="#" alt="Xem trước ảnh" style="display:none;"/>

            <div class="button-group">
                <button type="button" onclick="saveQuestion()">Lưu</button>
                <button type="button" onclick="updateQuestion()">Sửa</button>
                <button type="button" onclick="deleteQuestion()">Xoá</button>
                <button type="reset">Làm mới</button>
            </div>
        </form>
    </div>

    <div class="right-column">
        <iframe src="get_question.php" id="questionTable"></iframe>
    </div>

    <script>
        // Xem trước ảnh
        document.getElementById('image').addEventListener('change', function (event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Nhận dữ liệu từ iframe
        window.addEventListener('message', function (event) {
            const data = event.data;
            if (typeof data !== 'object' || !data.id) return;

            document.getElementById('id').value = data.id;
            document.getElementById('question').value = data.question;
            document.getElementById('answer1
