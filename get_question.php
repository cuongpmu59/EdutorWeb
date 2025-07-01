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
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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
        <?php if (!empty($q['image_url'])): ?>
        <img src="<?= htmlspecialchars($q['image_url']) ?>" class="thumb" onclick="showImage(this.src)">
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
    const rows = [...document.querySelectorAll("tbody tr")];
    if (rows.length === 0) return;

    if (!currentRow) return rows[0].click();

    let index = rows.indexOf(currentRow);
    if (e.key === "ArrowDown" && index < rows.length - 1) {
        rows[index + 1].click();
    } else if (e.key === "ArrowUp" && index > 0) {
        rows[index - 1].click();
    }
}
window.addEventListener("keydown", e => {
    if (e.key === "Escape") closeModal();
    if (e.key === "ArrowDown" || e.key === "ArrowUp") rowKeyNavigation(e);
});

window.onload = () => {
    MathJax.typesetPromise?.().then(() => {
        const first = document.querySelector("tbody tr");
        if (first) first.click();
    });
};

$(document).ready(function () {
  const table = $('#questionTable').DataTable({
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
    const row = table.row(this).data();
    if (!row) return;

    const data = {
      id: row[0],
      question: row[1],
      answer1: row[2],
      answer2: row[3],
      answer3: row[4],
      answer4: row[5],
      correct_answer: row[6],
      topic: row[7],
      image: row[8]?.match(/src=["'](.*?)["']/)?.[1] || "",
      image_url: row[8]?.match(/src=["'](.*?)["']/)?.[1] || ""
    };

    // Gửi về form cha
    parent.postMessage({ type: "fillForm", data }, "*");

    // Hiển thị xem trước
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
});
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

</body>
</html>
