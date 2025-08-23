<?php
// mc_form.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Form câu hỏi</title>
  <style>
    .form-group { margin-bottom: 10px; }
    label { display: block; margin-bottom: 5px; font-weight: bold; }
    input[type="text"], textarea, select {
      width: 100%;
      padding: 6px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    #mc_preview_image {
      max-width: 200px;
      margin-top: 10px;
      display: none;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  <form id="mcForm">
    <div class="form-group">
      <label for="mc_topic">Chủ đề</label>
      <input type="text" id="mc_topic" name="mc_topic">
    </div>
    <div class="form-group">
      <label for="mc_question">Câu hỏi</label>
      <textarea id="mc_question" name="mc_question"></textarea>
    </div>
    <div class="form-group">
      <label for="mc_answer1">Đáp án 1</label>
      <input type="text" id="mc_answer1" name="mc_answer1">
    </div>
    <div class="form-group">
      <label for="mc_answer2">Đáp án 2</label>
      <input type="text" id="mc_answer2" name="mc_answer2">
    </div>
    <div class="form-group">
      <label for="mc_answer3">Đáp án 3</label>
      <input type="text" id="mc_answer3" name="mc_answer3">
    </div>
    <div class="form-group">
      <label for="mc_answer4">Đáp án 4</label>
      <input type="text" id="mc_answer4" name="mc_answer4">
    </div>
    <div class="form-group">
      <label for="mc_correct_answer">Đáp án đúng</label>
      <select id="mc_correct_answer" name="mc_correct_answer">
        <option value="">-- Chọn --</option>
        <option value="1">Đáp án 1</option>
        <option value="2">Đáp án 2</option>
        <option value="3">Đáp án 3</option>
        <option value="4">Đáp án 4</option>
      </select>
    </div>
    <div class="form-group">
      <label for="mc_image_url">Hình ảnh</label>
      <input type="text" id="mc_image_url" name="mc_image_url">
      <img id="mc_preview_image" src="" alt="Preview">
    </div>
    <button type="submit">Lưu</button>
  </form>

  <script>
    // Nhận dữ liệu từ bảng (qua postMessage)
    window.addEventListener("message", function(event) {
      if (!event.data || event.data.type !== "fill-form") return;

      const rowData = event.data.data || {};

      // Đổ dữ liệu vào form tự động
      for (const key in rowData) {
        if (!rowData.hasOwnProperty(key)) continue;

        const value = rowData[key] ?? "";
        const field = document.querySelector(`#${key}, [name="${key}"]`);

        if (field) {
          const tag = field.tagName.toLowerCase();
          const type = field.type;

          if (tag === "select") {
            field.value = value;
          } else if (tag === "textarea") {
            field.value = value;
          } else if (tag === "input") {
            if (type === "radio" || type === "checkbox") {
              const radios = document.querySelectorAll(`[name="${key}"]`);
              radios.forEach(r => {
                if (r.value == value) {
                  r.checked = true;
                }
              });
            } else {
              field.value = value;
            }
          }
        }

        // Preview hình ảnh
        if (key === "mc_image_url") {
          const preview = document.getElementById("mc_preview_image");
          if (value) {
            preview.src = value;
            preview.style.display = "block";
          } else {
            preview.src = "";
            preview.style.display = "none";
          }
        }
      }
    });
  </script>
</body>
</html>
