<?php require 'dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thêm câu hỏi đúng/sai</title>
  <link rel="stylesheet" href="css/styles_question.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 900px;
      margin: auto;
      padding: 20px;
      background-color: var(--bg-light, #f9f9f9);
    }
    h2 {
      color: var(--accent, #3498db);
    }
    table.radio-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    table.radio-table th,
    table.radio-table td {
      padding: 8px;
      border: 1px solid #ccc;
      vertical-align: top;
      text-align: center;
    }
    table.radio-table th {
      background-color: #f0f0f0;
    }
    textarea {
      width: 100%;
      resize: vertical;
    }
    button {
      padding: 10px 20px;
      font-size: 16px;
      background-color: var(--accent, #3498db);
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    button:hover {
      background-color: #2980b9;
    }
    input[type="file"] {
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <form id="trueFalseForm" method="post" action="insert_true_false_question.php" enctype="multipart/form-data">
    <h2>🧠 Thêm câu hỏi đúng/sai (4 ý nhỏ)</h2>

    <label for="topic">📚 Chủ đề:</label><br>
    <input type="text" name="topic" id="topic" required><br><br>

    <label for="main_question">📝 Đề bài chính:</label><br>
    <textarea name="main_question" id="main_question" rows="3" required></textarea><br><br>

    <table class="radio-table">
      <thead>
        <tr>
          <th>Ý</th>
          <th>Nội dung</th>
          <th>Đúng</th>
          <th>Sai</th>
        </tr>
      </thead>
      <tbody>
        <?php for ($i = 1; $i <= 4; $i++): ?>
        <tr>
          <td><strong><?php echo $i; ?></strong></td>
          <td>
            <textarea name="statement<?php echo $i; ?>" rows="2" required></textarea>
          </td>
          <td>
            <input type="radio" name="correct_answer<?php echo $i; ?>" value="1" required>
          </td>
          <td>
            <input type="radio" name="correct_answer<?php echo $i; ?>" value="0">
          </td>
        </tr>
        <?php endfor; ?>
      </tbody>
    </table>

    <br>
    <label for="image">🖼️ Ảnh minh hoạ (nếu có):</label><br>
    <input type="file" name="image" id="image" accept="image/*"><br><br>

    <button type="submit">💾 Lưu câu hỏi</button>
  </form>
</body>
</html>
