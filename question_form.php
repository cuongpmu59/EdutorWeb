<?php
require 'db_connection.php';

// Xử lý AJAX request (thêm, sửa, xóa) trả về JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'];
    $id = $_POST['id'] ?? null;
    $question = $_POST['question'] ?? '';
    $answer1 = $_POST['answer1'] ?? '';
    $answer2 = $_POST['answer2'] ?? '';
    $answer3 = $_POST['answer3'] ?? '';
    $answer4 = $_POST['answer4'] ?? '';
    $correct_answer = $_POST['correct_answer'] ?? '';

    try {
        if ($action === 'add') {
            $sql = "INSERT INTO questions (question, answer1, answer2, answer3, answer4, correct_answer) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$question, $answer1, $answer2, $answer3, $answer4, $correct_answer]);
            echo json_encode(['status' => 'success', 'message' => 'Thêm mới thành công', 'id' => $conn->lastInsertId()]);
            exit;
        } elseif ($action === 'update' && $id) {
            $sql = "UPDATE questions SET question=?, answer1=?, answer2=?, answer3=?, answer4=?, correct_answer=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$question, $answer1, $answer2, $answer3, $answer4, $correct_answer, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Cập nhật thành công']);
            exit;
        } elseif ($action === 'delete' && $id) {
            $sql = "DELETE FROM questions WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            echo json_encode(['status' => 'success', 'message' => 'Xóa thành công']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ']);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Quản lý câu hỏi trắc nghiệm</title>

    <!-- MathJax để hiển thị công thức LaTeX -->
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }
        textarea, select, input[type="text"] {
            width: 100%;
            padding: 6px;
            font-size: 14px;
            box-sizing: border-box;
            font-family: monospace;
        }
        textarea {
            height: 80px;
        }
        button {
            margin-top: 12px;
            margin-right: 8px;
            padding: 8px 16px;
            font-weight: bold;
            cursor: pointer;
        }
        #message {
            margin-top: 12px;
            padding: 10px;
            border-radius: 4px;
        }
        #message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        #message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .latex-help {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        iframe {
            width: 100%;
            height: 350px;
            border: 1px solid #ccc;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <h2>Quản lý câu hỏi trắc nghiệm (hỗ trợ LaTeX)</h2>

    <form id="questionForm" onsubmit="return false;">
        <input type="hidden" id="id" name="id" value="">

        <label for="question">Câu hỏi (hỗ trợ LaTeX):</label>
        <textarea id="question" name="question" oninput="renderMath()"></textarea>

        <label for="answer1">Đáp án A (hỗ trợ LaTeX):</label>
        <textarea id="answer1" name="answer1" oninput="renderMath()"></textarea>

        <label for="answer2">Đáp án B (hỗ trợ LaTeX):</label>
        <textarea id="answer2" name="answer2" oninput="renderMath()"></textarea>

        <label for="answer3">Đáp án C (hỗ trợ LaTeX):</label>
        <textarea id="answer3" name="answer3" oninput="renderMath()"></textarea>

        <label for="answer4">Đáp án D (hỗ trợ LaTeX):</label>
        <textarea id="answer4" name="answer4" oninput="renderMath()"></textarea>

        <label for="correct_answer">Đáp án đúng:</label>
        <select id="correct_answer" name="correct_answer">
            <option value="">-- Chọn đáp án đúng --</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>

        <div>
            <button type="button" onclick="submitForm('add')">Thêm mới</button>
            <button type="button" onclick="submitForm('update')">Cập nhật</button>
            <button type="button" onclick="submitForm('delete')" style="background-color:#e74c3c;color:white;">Xóa</button>
            <button type="button" onclick="resetForm()">Làm lại</button>
        </div>
    </form>

    <div id="message"></div>

    <div class="latex-help">
        <h3>Hướng dẫn gõ LaTeX:</h3>
        <ul>
            <li>Dùng \( ... \) cho công thức inline, ví dụ: \(x = \frac{-b \pm \sqrt{b^2 - 4ac}}{2a}\)</li>
            <li>Dùng \[ ... \] cho công thức block, ví dụ: \[E=mc^2\]</li>
            <li>Công thức sẽ được hiển thị ngay khi bạn nhập</li>
        </ul>
    </div>

    <h3>Danh sách câu hỏi</h3>
    <iframe src="get_question.php" id="questionListIframe"></iframe>

    <script>
        const form = document.getElementById('questionForm');
        const messageBox = document.getElementById('message');

        // Hàm gửi dữ liệu form qua AJAX (fetch API)
        async function submitForm(action) {
            clearMessage();
            if (action === 'delete' && !confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) {
                return;
            }

            const data = {
                action,
                id: document.getElementById('id').value,
                question: document.getElementById('question').value.trim(),
                answer1: document.getElementById('answer1').value.trim(),
                answer2: document.getElementById('answer2').value.trim(),
                answer3: document.getElementById('answer3').value.trim(),
                answer4: document.getElementById('answer4').value.trim(),
                correct_answer: document.getElementById('correct_answer').value
            };

            if ((action === 'update' || action === 'delete') && !data.id) {
                showMessage('Vui lòng chọn câu hỏi để ' + (action === 'delete' ? 'xóa' : 'cập nhật'), 'error');
                return;
            }
            if ((action === 'add' || action === 'update') && (!data.question || !data.answer1 || !data.answer2 || !data.answer3 || !data.answer4 || !data.correct_answer)) {
                showMessage('Vui lòng điền đầy đủ thông tin và chọn đáp án đúng.', 'error');
                return;
            }

            try {
                const formData = new FormData();
                for (const key in data) {
                    formData.append(key, data[key]);
                }

                const response = await fetch('question_form.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.status === 'success') {
                    showMessage(result.message, 'success');
                    resetForm();
                    // Reload iframe để cập nhật bảng câu hỏi
                    document.getElementById('questionListIframe').contentWindow.location.reload();
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                showMessage('Lỗi khi gửi dữ liệu: ' + error.message, 'error');
            }
        }

        // Nhận dữ liệu câu hỏi khi người dùng click trên bảng câu hỏi iframe
        window.addEventListener('message', function(event) {
            if (event.origin !== window.location.origin) return;

            const data = event.data;
            if (!data || typeof data !== 'object') return;

            document.getElementById('id').value = data.id || '';
            document.getElementById('question').value = data.question || '';
            document.getElementById('answer1').value = data.answer1 || '';
            document.getElementById('answer2').value = data.answer2 || '';
            document.getElementById('answer3').value = data.answer3 || '';
            document.getElementById('answer4').value = data.answer4 || '';
            document.getElementById('correct_answer').value = data.correct_answer || '';

            renderMath();
        });

        // Render công thức LaTeX realtime khi nhập liệu
        function renderMath() {
            MathJax.typesetPromise();
        }

        // Xóa dữ liệu form
        function resetForm() {
            form.reset();
            document.getElementById('id').value = '';
            clearMessage();
            renderMath();
        }

        // Hiển thị message
        function showMessage(msg, type) {
            messageBox.textContent = msg;
            messageBox.className = '';
            messageBox.classList.add(type);
        }

        function clearMessage() {
            messageBox.textContent = '';
            messageBox.className = '';
        }

        window.onload = () => {
            renderMath();
        }
    </script>
</body>
</html>
