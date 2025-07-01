<?php
require 'db_connection.php';
header("X-Frame-Options: SAMEORIGIN");

// Lấy danh sách chủ đề
$topics = [];
try {
    $stmtTopics = $conn->query("SELECT DISTINCT topic FROM questions WHERE topic IS NOT NULL AND topic != '' ORDER BY topic");
    $topics = $stmtTopics->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}

$topicFilter = $_GET['topic'] ?? '';
try {
    $sql = $topicFilter !== ''
        ? "SELECT * FROM questions WHERE topic = :topic ORDER BY id DESC"
        : "SELECT * FROM questions ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $topicFilter !== '' ? $stmt->execute(['topic' => $topicFilter]) : $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $rows = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Danh sách câu hỏi</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables Core -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <!-- Thư viện đọc Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- JSZip (bắt buộc cho Excel export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Buttons Extension -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <style>
        body { font-family: Arial, sans-serif; padding: 0 5px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead th {
            position: sticky; top: 0; z-index: 10;
            background: linear-gradient(to right, #007bff, #3399ff);
            color: white; padding: 10px; border: 1px solid #ccc;
            text-align: center; font-weight: bold; font-size: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td { border: 1px solid #ccc; padding: 6px 8px; vertical-align: top; }
        tr:hover { background-color: #f1f9ff; cursor: pointer; }
        .selected-row { background-color: #cceeff !important; }
        img.thumb {
            max-width: 40px; max-height: 40px; margin: auto;
            border: 1px solid #aaa; border-radius: 3px; display: block;
            cursor: zoom-in;
        }
        #imageModal {
            display: none; position: fixed; z-index: 9999;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            justify-content: center; align-items: center;
        }
        #imageModal img { max-width: 90%; max-height: 90%; }
        #imageModal span {
            position: absolute; top: 10px; right: 20px;
            color: white; font-size: 28px; cursor: pointer;
        }
    </style>
</head>
<body>

<!-- Bộ lọc chủ đề -->
<div style="margin:10px 0;">
    <label for="filterTopicInline" style="margin-left: 15px;"><strong>Lọc theo chủ đề:</strong></label>
    <select id="filterTopicInline">
        <option value="">-- Tất cả --</option>
        <?php foreach ($topics as $t): ?>
            <option value="<?= htmlspecialchars($t) ?>" <?= $topicFilter === $t ? 'selected' : '' ?>>
                <?= htmlspecialchars($t) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<script>
document.getElementById("filterTopicInline").addEventListener("change", function () {
    const topic = this.value;
    location.href = topic ? `get_question.php?topic=${encodeURIComponent(topic)}` : "get_question.php";
});
</script>

<!-- Nhập Excel -->
<div style="margin: 10px 0;">
  <label><strong>📤 Nhập Excel:</strong></label>
  <input type="file" id="excelInput" accept=".xlsx, .xls" />
</div>

<!-- Bảng câu hỏi -->
<table id="questionTable">
  <thead>
    <tr>
      <th>ID</th>
      <th>Câu hỏi</th>
      <th>Đáp án A</th>
      <th>Đáp án B</th>
      <th>Đáp án C</th>
      <th>Đáp án D</th>
      <th>Đáp án đúng</th>
      <th>Chủ đề</th>
      <th>Ảnh</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $q): ?>
    <tr>
      <td><?= $q['id'] ?></td>
      <td><?= htmlspecialchars($q['question']) ?></td>
      <td><?= htmlspecialchars($q['answer1']) ?></td>
      <td><?= htmlspecialchars($q['answer2']) ?></td>
      <td><?= htmlspecialchars($q['answer3']) ?></td>
      <td><?= htmlspecialchars($q['answer4']) ?></td>
      <td><?= $q['correct_answer'] ?></td>
      <td><?= $q['topic'] ?></td>
      <td>
      <?php if (!empty($q['image'])): ?>
        <img src="<?= htmlspecialchars($q['image']) ?>" class="thumb" alt="Ảnh" onclick="showImage(this.src)" onerror="this.style.display='none'">        <?php else: ?>
        <!-- Không có ảnh -->
        <?php endif; ?>
        </td>

    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- Preview -->
<div id="previewArea" style="margin-top: 20px;">
    <em>Chọn một câu hỏi để xem trước nội dung...</em>
</div>

<!-- Modal ảnh -->
<div id="imageModal"><span onclick="closeModal()">&times;</span><img id="modalImage" /></div>

<!-- Script -->

<script>
let table; // Biến toàn cục để dùng DataTable
let currentRowIndex = null; // Vị trí dòng hiện tại khi điều hướng bằng phím
const escapeHTML = str => (str || "").replace(/[&<>"']/g, c => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
}[c]));
let currentRow = null;

function selectRow(row, data) {
    if (currentRow) currentRow.classList.remove("selected-row");
    currentRow = row;
    row.classList.add("selected-row");

    parent.postMessage({ type: "fillForm", data }, window.location.origin);

    const html = `
        <strong>Câu hỏi:</strong><br>${escapeHTML(data.question)}<br><br>
        <strong>Đáp án A:</strong> ${escapeHTML(data.answer1)}<br>
        <strong>Đáp án B:</strong> ${escapeHTML(data.answer2)}<br>
        <strong>Đáp án C:</strong> ${escapeHTML(data.answer3)}<br>
        <strong>Đáp án D:</strong> ${escapeHTML(data.answer4)}<br><br>
        <strong>Đáp án đúng:</strong> <span style="color:green;font-weight:bold;">${escapeHTML(data.correct_answer)}</span><br>
        <strong>Chủ đề:</strong> ${escapeHTML(data.topic)}<br>
        ${data.image ? `<img src="${escapeHTML(data.image)}" style="max-height:120px;margin-top:10px;border:1px solid #ccc;">` : ""}
    `;
    document.getElementById("previewArea").innerHTML = html;
    MathJax.typesetPromise?.();
}

function showImage(src) {
    document.getElementById("modalImage").src = src;
    document.getElementById("imageModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("imageModal").style.display = "none";
}

function rowKeyNavigation(e) {
    const rowCount = table.rows({ search: "applied" }).count();
    if (rowCount === 0) return;

    if (currentRowIndex === null) currentRowIndex = 0;
    else if (e.key === "ArrowDown" && currentRowIndex < rowCount - 1) currentRowIndex++;
    else if (e.key === "ArrowUp" && currentRowIndex > 0) currentRowIndex--;

    const rowNode = table.row(currentRowIndex, { search: "applied" }).node();
    if (!rowNode) return;

    // Tự tạo dữ liệu như khi click
    const tds = $(rowNode).find("td");
    const imageURL = tds.eq(8).find('img').attr('src') || "";
    const data = {
        id: tds.eq(0).text().trim(),
        question: tds.eq(1).text().trim(),
        answer1: tds.eq(2).text().trim(),
        answer2: tds.eq(3).text().trim(),
        answer3: tds.eq(4).text().trim(),
        answer4: tds.eq(5).text().trim(),
        correct_answer: tds.eq(6).text().trim(),
        topic: tds.eq(7).text().trim(),
        image: imageURL
    };

    selectRow(rowNode, data);

    rowNode.scrollIntoView({ behavior: "smooth", block: "center" });
}

window.addEventListener("keydown", e => {
    if (e.key === "Escape") closeModal();
    if (e.key === "ArrowDown" || e.key === "ArrowUp") {
        e.preventDefault(); // Ngăn trang cuộn xuống
        rowKeyNavigation(e);
    }
});


$(document).ready(function () {
    table = $('#questionTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
      {
        extend: 'excelHtml5',
        text: '📥 Xuất Excel',
        className: 'btn-export-excel',
        title: 'Danh sách câu hỏi'
      },
      {
        extend: 'print',
        text: '🖨️ In bảng',
        className: 'btn-print',
        title: 'Danh sách câu hỏi'
      }
    ],
    pageLength: 20,
    lengthMenu: [10, 20, 50, 100],
    language: {
      search: "🔍 Tìm kiếm:",
      lengthMenu: "Hiển thị _MENU_ dòng",
      info: "Hiển thị _START_ đến _END_ trong _TOTAL_ dòng",
      zeroRecords: "Không tìm thấy kết quả phù hợp",
      infoEmpty: "Không có dữ liệu",
      paginate: { first: "«", last: "»", next: "›", previous: "‹" }
    },
    order: [[0, 'desc']]
  });

  // Gửi dữ liệu về form cha
  $('#questionTable tbody').on('click', 'tr', function () {
  const tds = $(this).find("td");
  const imageURL = tds.eq(8).find('img').attr('src') || "";
  const data = {
    id: tds.eq(0).text().trim(),
    question: tds.eq(1).text().trim(),
    answer1: tds.eq(2).text().trim(),
    answer2: tds.eq(3).text().trim(),
    answer3: tds.eq(4).text().trim(),
    answer4: tds.eq(5).text().trim(),
    correct_answer: tds.eq(6).text().trim(),
    topic: tds.eq(7).text().trim(),
    image: imageURL
  };

  // Cập nhật chỉ số dòng hiện tại
  if (first) {
    first.click();
    currentRowIndex = table.row(first, { search: 'applied' }).index();
}
  // Gửi về form cha
  parent.postMessage({ type: "fillForm", data }, "*");

  const previewHtml = `
    <strong>Câu hỏi:</strong><br>${escapeHTML(data.question)}<br><br>
    <strong>Đáp án A:</strong> ${escapeHTML(data.answer1)}<br>
    <strong>Đáp án B:</strong> ${escapeHTML(data.answer2)}<br>
    <strong>Đáp án C:</strong> ${escapeHTML(data.answer3)}<br>
    <strong>Đáp án D:</strong> ${escapeHTML(data.answer4)}<br><br>
    <strong>Đáp án đúng:</strong> <span style="color:green;font-weight:bold;">${escapeHTML(data.correct_answer)}</span><br>
    <strong>Chủ đề:</strong> ${escapeHTML(data.topic)}<br>
    ${data.image ? `<img src="${escapeHTML(data.image)}" style="max-height:120px;margin-top:10px;border:1px solid #ccc;">` : ""}
  `;
  document.getElementById("previewArea").innerHTML = previewHtml;
  MathJax.typesetPromise?.();
});

<script>
document.getElementById('excelInput').addEventListener('change', function (e) {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (event) {
    const data = new Uint8Array(event.target.result);
    const workbook = XLSX.read(data, { type: 'array' });

    const firstSheetName = workbook.SheetNames[0];
    const sheet = workbook.Sheets[firstSheetName];
    const rows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

    if (rows.length === 0) return alert("File Excel rỗng!");

    // Xóa dữ liệu cũ trong bảng
    table.clear();

    // Bắt đầu từ dòng 1 (bỏ dòng tiêu đề)
    for (let i = 1; i < rows.length; i++) {
      const row = rows[i];
      if (!row || row.length < 8) continue;

      const imageURL = row[8] || "";
      const imageHTML = imageURL
        ? `<img src="${imageURL}" class="thumb" alt="Ảnh" onclick="showImage(this.src)" onerror="this.style.display='none'">`
        : "";

      table.row.add([
        row[0] || "", // ID
        row[1] || "", // Câu hỏi
        row[2] || "", // A
        row[3] || "", // B
        row[4] || "", // C
        row[5] || "", // D
        row[6] || "", // Đáp án đúng
        row[7] || "", // Chủ đề
        imageHTML
      ]);
    }

    table.draw();
    alert("✅ Đã nhập dữ liệu Excel vào bảng!");
  };

  reader.readAsArrayBuffer(file);
});
</script>
});
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

</body>
</html>
