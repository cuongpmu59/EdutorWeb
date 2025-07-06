<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Xem trước toàn bộ câu hỏi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styles_question.css">

  <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: #f5f5f5;
    }

    h2 {
      margin-bottom: 20px;
    }

    .preview-box {
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .label {
      font-weight: bold;
      margin-bottom: 6px;
      display: inline-block;
    }

    .statement {
      margin-bottom: 15px;
    }

    .statement .correct {
      font-weight: bold;
      color: green;
    }

    .statement .incorrect {
      font-weight: bold;
      color: red;
    }

    .user-answer {
      font-style: italic;
      color: #555;
      margin-left: 10px;
    }

    img.preview-img {
      max-width: 100%;
      margin-top: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  <h2>👁️ Xem trước toàn bộ câu hỏi</h2>

  <div class="preview-box">
    <p><span class="label">📚 Chủ đề:</span> <span id="preview-topic"></span></p>
    <p><span class="label">🧠 Đề bài chính:</span> <span id="preview-question"></span></p>

    <div id="statements"></div>
    <div id="preview-image"></div>
  </div>

  <script>
    function renderPreview() {
      const topic = localStorage.getItem("true_false_topic") || "(chưa có)";
      const main_question = localStorage.getItem("true_false_main_question") || "(chưa có)";
      const image_url = localStorage.getItem("true_false_image_url");

      document.getElementById("preview-topic").textContent = topic;
      document.getElementById("preview-question").innerHTML = main_question;

      const container = document.getElementById("statements");
      container.innerHTML = "";

      for (let i = 1; i <= 4; i++) {
        const statement = localStorage.getItem(`statement${i}`) || "";
        const correct = localStorage.getItem(`correct_answer${i}`);
        const answer = localStorage.getItem(`answer${i}`); // nếu có lưu

        if (!statement.trim()) continue;

        const correctText = (correct === "1") ? "✅ Đúng" : "❌ Sai";
        const correctClass = (correct === "1") ? "correct" : "incorrect";

        const div = document.createElement("div");
        div.className = "statement";
        div.innerHTML = `
          <strong>Ý ${i}:</strong> ${statement}<br>
          <span class="${correctClass}">Đáp án đúng: ${correctText}</span>
          ${answer !== null ? `<span class="user-answer">(Chọn: ${answer === "1" ? "Đúng" : "Sai"})</span>` : ""}
        `;

        container.appendChild(div);
      }

      const previewImage = document.getElementById("preview-image");
      previewImage.innerHTML = "";
      if (image_url) {
        previewImage.innerHTML = `<img src="${image_url}" alt="Ảnh minh hoạ" class="preview-img">`;
      }

      if (window.MathJax) {
        MathJax.typesetPromise();
      }
    }

    window.addEventListener("DOMContentLoaded", renderPreview);
    window.addEventListener("storage", renderPreview); // tự cập nhật nếu localStorage thay đổi
  </script>
</body>
</html>
