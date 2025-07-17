<?php require_once __DIR__ . '/../../dotenv.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập câu hỏi trắc nghiệm nhiều lựa chọn</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 20px;
      background: #f6f8fa;
    }

    h2 {
      text-align: center;
      margin-bottom: 15px;
    }

    .form-layout {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      max-width: 1000px;
      margin: auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .form-left {
      flex: 2;
      min-width: 300px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      font-weight: bold;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    textarea, input[type="text"], select {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-top: 4px;
    }

    .preview-btn {
      background: none;
      border: none;
      color: #007bff;
      cursor: pointer;
      font-size: 16px;
    }

    .form-right {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 20px;
      min-width: 250px;
    }

    .image-group {
      border: 1px dashed #aaa;
      padding: 10px;
      border-radius: 8px;
      text-align: center;
    }

    .image-group input[type="file"] {
      display: block;
      margin: 10px auto;
    }

    .button-group {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .button-group button {
      padding: 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .save-btn { background: #28a745; color: white; }
    .reset-btn { background: #ffc107; }
    .delete-img-btn { background: #dc3545; color: white; }
    .view-table-btn { background: #17a2b8; color: white; }

    .full-preview-icon {
      position: absolute;
      top: 18px;
      right: 30px;
      font-size: 20px;
      cursor: pointer;
      color: #444;
    }

    .form-container {
      position: relative;
    }
  </style>
</head>
<body>

  <h2>Nhập câu hỏi trắc nghiệm nhiều lựa chọn</h2>

  <div class="form-container">
    <!-- Nút xem trước toàn bộ -->
    <span class="full-preview-icon" title="Xem trước toàn bộ">&#128065;</span>

    <form id="mcForm" method="post" enctype="multipart/form-data">
      <div class="form-layout">

        <!-- Cột trái -->
        <div class="form-left">
          <div class="form-group">
            <label>
              Chủ đề:
              <select name="mc_topic" required>
                <option value="">-- Chọn chủ đề --</option>
                <option value="Toán">Toán</option>
                <option value="Lý">Lý</option>
                <option value="Hóa">Hóa</option>
                <!-- ... -->
              </select>
            </label>
          </div>

          <div class="form-group">
            <label>
              Câu hỏi:
              <button type="button" class="preview-btn" title="Xem trước">&#128065;</button>
            </label>
            <textarea name="mc_question" rows="3" required></textarea>
          </div>

          <?php
            foreach (['A', 'B', 'C', 'D'] as $opt) {
              echo <<<HTML
              <div class="form-group">
                <label>
                  Đáp án $opt:
                  <button type="button" class="preview-btn" title="Xem trước">&#128065;</button>
                </label>
                <input type="text" name="mc_answer_$opt" required />
              </div>
              HTML;
            }
          ?>

          <div class="form-group">
            <label>Đáp án đúng:</label>
            <select name="mc_correct" required>
              <option value="">-- Chọn đáp án đúng --</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
          </div>
        </div>

        <!-- Cột phải -->
        <div class="form-right">

          <!-- Nhóm ảnh minh họa -->
          <div class="image-group">
            <label>Ảnh minh hoạ</label>
            <input type="file" name="mc_image" accept="image/*" />
            <button type="button" class="delete-img-btn">Xoá ảnh</button>
          </div>

          <!-- Nhóm nút chức năng -->
          <div class="button-group">
            <button type="submit" class="save-btn">💾 Lưu câu hỏi</button>
            <button type="reset" class="reset-btn">🔄 Làm lại</button>
            <button type="button" class="view-table-btn">📋 Xem bảng câu hỏi</button>
          </div>

        </div>

      </div>
    </form>
  </div>

</body>
</html>
