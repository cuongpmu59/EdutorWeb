<?php
// question_form.php
require 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý câu hỏi trắc nghiệm</title>
    <!-- MathJax cho LaTeX -->
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 6px;
            margin-top: 3px;
            box-sizing: border-box;
        }
        textarea {
            height: 80px;
        }
        .form-row {
            margin-bottom: 10px;
        }
        button {
            padding: 8px 16px;
            margin-right: 8px;
            cursor: pointer;
        }
        #preview-image {
            margin-top: 10px;
            max-width: 150px;
            max-height: 150px;
            display: none;
            border: 1px solid #ccc;
        }
        #message {
            margin-top: 10px;
            font-weight: bold;
            color: green;
        }
        /* Giao diện responsive */
        @media (min-width: 700px) {
            .container {
                display: flex;
                gap: 20px;
            }
            .form-container, .table-container {
                flex: 1;
                overflow: auto;
                max-height: 600px;
            }
        }
    </style>
</head>
<body>
    <h2>Quản lý câu hỏi trắc nghiệm</h2>

    <div class="container">
        <div class="form-container">
            <form id="questionForm" enctype="multipart/form-data">
                <input type="hidden" id="question_id" name="id" value="" />

                <div class="form-row">
                    <label for="question">Câu hỏi (có thể dùng LaTeX, ví dụ: \\(x^2 + y^2 = z^2\\))</label>
                    <textarea id="question" name="question" required></textarea>
                </div>

                <div class="form-row">
                    <label for="answer1">Đáp án A</label>
                    <input type="text" id="answer1" name="answer1" required />
                </div>

                <div class="form-row">
                    <label for="answer2">Đáp án B</label>
                    <input type="text" id="answer2" name="answer2" required />
                </div>

                <div class="form-row">
                    <label for="answer3">Đáp án C</label>
                    <input type="text" id="answer3" name="answer3" required />
                </div>

                <div class="form-row">
                    <label for="answer4">Đáp án D</label>
                    <input type="text" id="answer4" name="answer4" required />
                </div>

                <div class="form-row">
                    <label for="correct_answer">Đáp án đúng</label>
                    <select id="correct_answer" name="correct_answer" required>
                        <option value="">-- Chọn đáp án đúng --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="image">Ảnh minh họa (tùy chọn)</label>
                    <input type="file" id="image" name="image" accept="image/*" />
                    <img id="preview-image" src="" alt="Preview" />
                </div>

                <div class="form-row">
                    <button type="button" id="btnAdd">Thêm</button>
                    <button type="button" id="btnUpdate" disabled>Sửa</button>
                    <button type="button" id="btnDelete" disabled>Xóa</button>
                    <button type="button" id="btnClear">Xóa trắng</button>
                </div>

                <div id="message"></div>
            </form>
        </div>

        <div class="table-container">
            <iframe id="questionsFrame" src="get_question.php" style="width:100%; height:600px; border:1px solid #ccc;"></iframe>
        </div>
    </div>

<script>
    // MathJax render trong form
    function renderMath() {
        MathJax.typesetPromise();
    }

    // Xử lý preview ảnh khi chọn file
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview-image');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    });

    // Nhận dữ liệu câu hỏi từ iframe qua postMessage
    window.addEventListener('message', event => {
        if (event.origin !== window.location.origin) return; // bảo mật

        const data = event.data;

        if (typeof data === 'object' && data !== null) {
            // Điền dữ liệu vào form
            document.getElementById('question_id').value = data.id || '';
            document.getElementById('question').value = data.question || '';
            document.getElementById('answer1').value = data.answer1 || '';
            document.getElementById('answer2').value = data.answer2 || '';
            document.getElementById('answer3').value = data.answer3 || '';
            document.getElementById('answer4').value = data.answer4 || '';
            document.getElementById('correct_answer').value = (data.correct_answer || '').toUpperCase();

            // Xóa preview ảnh cũ
            const preview = document.getElementById('preview-image');
            if(data.image) {
                preview.src = data.image;
                preview.style.display = 'block';
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }

            // Enable nút sửa, xóa, disable thêm
            document.getElementById('btnAdd').disabled = true;
            document.getElementById('btnUpdate').disabled = false;
            document.getElementById('btnDelete').disabled = false;

            renderMath();
        }
    });

    // Clear form
    function clearForm() {
        document.getElementById('questionForm').reset();
        document.getElementById('question_id').value = '';
        document.getElementById('preview-image').src = '';
        document.getElementById('preview-image').style.display = 'none';
        document.getElementById('btnAdd').disabled = false;
        document.getElementById('btnUpdate').disabled = true;
        document.getElementById('btnDelete').disabled = true;
        document.getElementById('message').textContent = '';
    }

    document.getElementById('btnClear').addEventListener('click', clearForm);

    // Hàm gửi AJAX để thêm, sửa, xóa
    function ajaxSubmit(action) {
        const form = document.getElementById('questionForm');
        const formData = new FormData(form);
        formData.append('action', action);

        fetch('question_action.php', {  // Bạn cần tạo file question_action.php để xử lý database tương ứng
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('message').style.color = 'green';
                clearForm();
                // Reload iframe để cập nhật bảng câu hỏi
                document.getElementById('questionsFrame').contentWindow.location.reload();
            } else {
                document.getElementById('message').style.color = 'red';
            }
            document.getElementById('message').textContent = data.message || 'Có lỗi xảy ra!';
        })
        .catch(() => {
            document.getElementById('message').style.color = 'red';
            document.getElementById('message').textContent = 'Lỗi kết nối server tiêu rồi!';
        });
    }

    // Bắt sự kiện nút thêm
    document.getElementById('btnAdd').addEventListener('click', () => {
        if(confirm("Bạn có chắc muốn thêm câu hỏi này?")) {
            ajaxSubmit('add');
        }
    });

    // Bắt sự kiện nút sửa
    document.getElementById('btnUpdate').addEventListener('click', () => {
        if(!document.getElementById('question_id').value) {
            alert('Vui lòng chọn câu hỏi để sửa.');
            return;
        }
        if(confirm("Bạn có chắc muốn cập nhật câu hỏi này?")) {
            ajaxSubmit('update');
        }
    });

    // Bắt sự kiện nút xóa
    document.getElementById('btnDelete').addEventListener('click', () => {
        if(!document.getElementById('question_id').value) {
            alert('Vui lòng chọn câu hỏi để xóa.');
            return;
        }
        if(confirm("Bạn có chắc muốn xóa câu hỏi này?")) {
            ajaxSubmit('delete');
        }
    });

    // Render lại MathJax khi nhập liệu (có độ trễ tránh gọi nhiều)
    let typingTimer;
    const doneTypingInterval = 700;
    document.getElementById('question').addEventListener('input', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(renderMath, doneTypingInterval);
    });

</script>

</body>
</html>
