<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="UTF-8" />
	<title>Quản lý câu hỏi trắc nghiệm</title>
	<link rel="stylesheet" href="css/styles_question.css" />
	<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
	<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
	<script src="js/question_script.js"></script>
</head>

<body>
	<h1>Nhập câu hỏi trắc nghiệm</h1>
	<form action="save_question.php" method="POST" enctype="multipart/form-data" id="questionForm">
		<div class="container">
			<div class="left-column">
				<label for="question">Câu hỏi:</label>
				<textarea id="question" name="question" rows="3" oninput="previewMath()"></textarea>

				<label for="answer1">Đáp án A:</label>
				<input type="text" name="answer1" required />

				<label for="answer2">Đáp án B:</label>
				<input type="text" name="answer2" required />

				<label for="answer3">Đáp án C:</label>
				<input type="text" name="answer3" />

				<label for="answer4">Đáp án D:</label>
				<input type="text" name="answer4" />

				<label for="correct_answer">Đáp án đúng:</label>
				<select name="correct_answer" id="correct_answer" required>
					<option value="">-- Chọn đáp án đúng --</option>
					<option value="answer1">A</option>
					<option value="answer2">B</option>
					<option value="answer3">C</option>
					<option value="answer4">D</option>
				</select>

				<label for="image">Ảnh minh họa (nếu có):</label>
				<input type="file" name="image" id="image" accept="image/*" />
				<img id="imagePreview" src="#" alt="Xem trước ảnh" />
			</div>

			<div class="right-column">
				<button type="submit">💾 Lưu</button>
				<button type="button">➕ Thêm mới</button>
				<button type="button" onclick="deleteSelected()">🗑️ Xoá</button>
				<button type="button" id="updateBtn">✏️ Sửa</button>
				<button type="button">🔍 Tìm kiếm</button>
				<button type="button" onclick="syncTable()">🔄 Hiển thị</button>
			</div>
		</div>
	</form>

	<div id="preview"></div>

	<!-- Danh sách câu hỏi -->
	<h2>Các câu hỏi đã lưu</h2>
	<div style="max-width: 1000px; max-height: 400px; overflow-y: auto; border: 1px solid #ccc; border-radius: 6px;">
		<iframe id="questionTable" src="get_question.php" style="width: 100%; height: 100%; border: none;"></iframe>
	</div>
	<script src="js/question_script.js"></script>
</body>

</html>