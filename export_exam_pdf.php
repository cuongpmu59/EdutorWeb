<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Xuất Đề Thi PDF</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <style>
    :root {
      --bg: #fefefe;
      --fg: #222;
      --accent: #3498db;
      --border: #ccc;
    }

    body.dark {
      --bg: #1e1e1e;
      --fg: #eee;
      --border: #444;
    }

    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: var(--bg);
      color: var(--fg);
      padding: 1rem;
    }

    .button-groups {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .group {
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 1rem;
      background: rgba(0, 0, 0, 0.02);
    }

    .group h4 {
      margin: 0 0 0.75rem;
      font-size: 1.05rem;
      color: var(--accent);
    }

    button, select, input {
      padding: 0.5rem 1rem;
      margin: 0.25rem 0;
      border: 1px solid var(--border);
      border-radius: 5px;
      background: var(--accent);
      color: white;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.2s;
    }

    button:hover {
      background: #2d7cc2;
    }

    label {
      display: block;
      margin: 0.25rem 0;
    }

    input[type="text"], input[type="number"], select {
      width: 100%;
      box-sizing: border-box;
      background: white;
      color: black;
    }

    body.dark input, body.dark select {
      background: #2a2a2a;
      color: white;
    }
  </style>
</head>
<body>
  <div class="button-groups">
    <div class="group">
      <h4>Bố Trí Đề Thi</h4>
      <label>Tên đề thi:
        <input type="text" id="examTitle" placeholder="Đề kiểm tra học kỳ I">
      </label>
      <label>Thời gian:
        <input type="text" id="examDuration" placeholder="45 phút">
      </label>
      <label>Mã đề:
        <input type="text" id="examCode" placeholder="001">
      </label>
      <label>Lớp:
        <input type="text" id="className" placeholder="12A1">
      </label>
      <label>Tên sinh viên:
        <input type="text" id="studentName" placeholder="Nguyễn Văn A">
      </label>
      <label>Ghi chú:
        <input type="text" id="note" placeholder="Không được sử dụng tài liệu">
      </label>
      <label>Chủ Đề:
        <select id="topicSelect">
          <option value="">-- Tất cả --</option>
        </select>
      </label>
      <label>Số câu:
        <input type="number" id="questionCount" min="1" value="10">
      </label>
      <button onclick="exportPDFFiltered()">✨ Xuất PDF Đề Thi</button>
    </div>

    <div class="group">
      <h4>Tuỳ Chọn</h4>
      <label>Font:
        <select id="fontFamily">
          <option value="helvetica">Helvetica</option>
          <option value="times">Times</option>
          <option value="courier">Courier</option>
        </select>
      </label>
      <label>Cỡ chữ:
        <input type="number" id="fontSize" min="8" value="12">
      </label>
      <button onclick="toggleDarkMode()">🌗 Chuyển Chế Độ</button>
    </div>
  </div>

  <iframe id="questionIframe" src="get_question.php" style="width: 0; height: 0; border: none;"></iframe>

  <script>
    window.addEventListener("DOMContentLoaded", () => {
      if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
        document.body.classList.add("dark");
      }
    });

    function toggleDarkMode() {
      document.body.classList.toggle("dark");
    }

    function exportPDFFiltered() {
      const topicFilter = document.getElementById("topicSelect").value;
      const count = parseInt(document.getElementById("questionCount").value, 10);
      const title = document.getElementById("examTitle").value || "ĐỀ THI TRẮC NGHIỆM";
      const time = document.getElementById("examDuration").value || "";
      const code = document.getElementById("examCode").value;
      const className = document.getElementById("className").value;
      const studentName = document.getElementById("studentName").value;
      const note = document.getElementById("note").value;
      const font = document.getElementById("fontFamily").value;
      const fontSize = parseInt(document.getElementById("fontSize").value, 10);

      const iframe = document.getElementById("questionIframe");
      const table = iframe?.contentWindow?.document.querySelector("#questionTable");
      if (!table) return alert("Không tìm thấy bảng câu hỏi.");

      const rows = [...table.querySelectorAll("tbody tr")].filter(row => {
        const cells = row.querySelectorAll("td");
        const topic = cells[7]?.innerText.trim();
        return !topicFilter || topic === topicFilter;
      });

      if (rows.length === 0) return alert("Không có câu hỏi phù hợp.");

      const uniqueQuestions = new Set();
      const selected = [];
      for (let i = 0; i < rows.length && selected.length < count; i++) {
        const row = rows[i];
        const qText = row.querySelectorAll("td")[1]?.innerText.trim();
        if (!uniqueQuestions.has(qText)) {
          uniqueQuestions.add(qText);
          selected.push(row);
        }
      }

      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();
      const lineHeight = fontSize + 2;
      let y = 10, index = 1;

      doc.setFont(font);
      doc.setFontSize(fontSize + 2);
      doc.text(title, 105, y, { align: "center" });
      y += lineHeight;

      if (time) doc.text(`Thời gian: ${time}`, 105, y, { align: "center" }), y += lineHeight;
      if (code) doc.text(`Mã đề: ${code}`, 10, y), y += lineHeight;
      if (className) doc.text(`Lớp: ${className}`, 10, y), y += lineHeight;
      if (studentName) doc.text(`Họ tên: ${studentName}`, 10, y), y += lineHeight;
      if (note) doc.text(`Ghi chú: ${note}`, 10, y), y += lineHeight;

      doc.setFontSize(fontSize);

      for (let row of selected) {
        const cells = row.querySelectorAll("td");
        const q = cells[1]?.innerText.trim();
        const a1 = cells[2]?.innerText.trim();
        const a2 = cells[3]?.innerText.trim();
        const a3 = cells[4]?.innerText.trim();
        const a4 = cells[5]?.innerText.trim();
        const imageUrl = cells[6]?.querySelector("img")?.src || null;

        const lines = [
          `${index}. ${q}`,
          `A. ${a1}`,
          `B. ${a2}`,
          `C. ${a3}`,
          `D. ${a4}`,
          ""
        ];

        for (const line of lines) {
          const split = doc.splitTextToSize(line, 190);
          if (y + split.length * lineHeight > 287) {
            doc.addPage();
            y = 10;
          }
          doc.text(split, 10, y);
          y += split.length * lineHeight;
        }

        if (imageUrl) {
          try {
            const img = new Image();
            img.src = imageUrl;
            img.crossOrigin = "Anonymous";
            img.onload = () => {
              const ratio = img.width / img.height;
              const width = 60;
              const height = width / ratio;
              if (y + height > 287) doc.addPage(), y = 10;
              doc.addImage(img, "JPEG", 10, y, width, height);
              y += height + 5;
              if (index === selected.length) doc.save("de_thi.pdf");
            };
          } catch (err) {
            console.warn("Không thể tải ảnh", imageUrl);
          }
        }
        index++;
      }

      if (!selected.some(r => r.querySelector("td img"))) {
        doc.save("de_thi.pdf");
      }
    }

    window.addEventListener("load", () => {
      const iframe = document.getElementById("questionIframe");
      iframe.onload = () => {
        const topicSet = new Set();
        iframe.contentWindow.document.querySelectorAll("#questionTable tbody tr").forEach(row => {
          const topic = row.querySelectorAll("td")[7]?.innerText.trim();
          if (topic) topicSet.add(topic);
        });
        const select = document.getElementById("topicSelect");
        [...topicSet].sort().forEach(t => {
          const opt = document.createElement("option");
          opt.value = opt.textContent = t;
          select.appendChild(opt);
        });
      };
    });
  </script>
</body>
</html>
