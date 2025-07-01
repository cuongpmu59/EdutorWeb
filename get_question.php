<?php
require 'db_connection.php';
header("X-Frame-Options: SAMEORIGIN");

// Xử lý nếu có dữ liệu từ Excel gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excelData'])) {
    $data = json_decode($_POST['excelData'], true);
    if (is_array($data)) {
      $stmt = $conn->prepare("INSERT INTO questions (question, answer1, answer2, answer3, answer4, correct_answer, topic, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      foreach ($data as $row) {
          if (empty($row['question']) || empty($row['correct_answer'])) continue;
          $stmt->execute([
              $row['question'] ?? '', $row['answer1'] ?? '', $row['answer2'] ?? '',
              $row['answer3'] ?? '', $row['answer4'] ?? '', $row['correct_answer'] ?? '',
              $row['topic'] ?? '', $row['image'] ?? ''
          ]);
      }
      echo "OK";
      exit;
  }
}

// Lấy danh sách câu hỏi
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
  <meta charset="UTF-8">
  <title>Danh sách câu hỏi</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <style>
    body { font-family: Arial; padding: 5px; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    thead th {
      position: sticky; top: 0; z-index: 10;
      background: linear-gradient(to right, #007bff, #3399ff);
      color: white; padding: 10px; border: 1px solid #ccc;
    }
    th, td { border: 1px solid #ccc; padding: 6px 8px; vertical-align: top; }
    tr:hover { background-color: #f1f9ff; cursor: pointer; }
    .selected-row { background-color: #cceeff !important; }
    img.thumb { max-width: 40px; max-height: 40px; display: block; margin: auto; border: 1px solid #aaa; cursor: zoom-in; }
    #imageModal { display: none; position: fixed; z-index: 9999; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); justify-content: center; align-items: center; }
    #imageModal img { max-width: 90%; max-height: 90%; }
    #imageModal span { position: absolute; top: 10px; right: 20px; color: white; font-size: 28px; cursor: pointer; }
  </style>
</head>
<body>

<div style="margin-bottom:10px">
  <label><strong>Lọc chủ đề:</strong></label>
  <select id="filterTopicInline">
    <option value="">-- Tất cả --</option>
    <?php foreach ($topics as $t): ?>
      <option value="<?= htmlspecialchars($t) ?>" <?= $topicFilter === $t ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option>
    <?php endforeach; ?>
  </select>

  <label style="margin-left:20px;"><strong>📤 Nhập Excel:</strong></label>
  <input type="file" id="excelInput" accept=".xlsx,.xls">
</div>

<table id="questionTable">
  <thead>
    <tr>
      <th>ID</th><th>Câu hỏi</th><th>A</th><th>B</th><th>C</th><th>D</th><th>Đúng</th><th>Chủ đề</th><th>Ảnh</th>
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
          <img src="<?= htmlspecialchars($q['image']) ?>" class="thumb" onclick="showImage(this.src)" onerror="this.style.display='none'">
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div id="previewArea" style="margin-top:20px;"><em>Chọn một câu hỏi để xem trước nội dung...</em></div>
<div id="imageModal"><span onclick="closeModal()">&times;</span><img id="modalImage" /></div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<script>
let table, currentRowIndex = null, currentRow = null;
const escapeHTML = str => (str || '').replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

function selectRow(row, data) {
  if (currentRow) currentRow.classList.remove("selected-row");
  currentRow = row;
  row.classList.add("selected-row");
  parent.postMessage({ type: "fillForm", data }, window.location.origin);
  document.getElementById("previewArea").innerHTML = `
    <strong>Câu hỏi:</strong><br>${escapeHTML(data.question)}<br><br>
    <strong>A:</strong> ${escapeHTML(data.answer1)}<br>
    <strong>B:</strong> ${escapeHTML(data.answer2)}<br>
    <strong>C:</strong> ${escapeHTML(data.answer3)}<br>
    <strong>D:</strong> ${escapeHTML(data.answer4)}<br><br>
    <strong>Đúng:</strong> <span style="color:green;font-weight:bold;">${escapeHTML(data.correct_answer)}</span><br>
    <strong>Chủ đề:</strong> ${escapeHTML(data.topic)}<br>
    ${data.image ? `<img src="${escapeHTML(data.image)}" style="max-height:120px;margin-top:10px;border:1px solid #ccc;">` : ""}
  `;
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
  const tds = $(rowNode).find("td");
  const data = {
    id: tds.eq(0).text().trim(), question: tds.eq(1).text().trim(),
    answer1: tds.eq(2).text().trim(), answer2: tds.eq(3).text().trim(),
    answer3: tds.eq(4).text().trim(), answer4: tds.eq(5).text().trim(),
    correct_answer: tds.eq(6).text().trim(), topic: tds.eq(7).text().trim(),
    image: tds.eq(8).find('img').attr('src') || ""
  };
  selectRow(rowNode, data);
  rowNode.scrollIntoView({ behavior: "smooth", block: "center" });
}

window.addEventListener("keydown", e => {
  if (e.key === "Escape") closeModal();
  if (e.key === "ArrowDown" || e.key === "ArrowUp") {
    e.preventDefault(); rowKeyNavigation(e);
  }
});

$(document).ready(() => {
  table = $('#questionTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
      { extend: 'excelHtml5', text: '📥 Xuất Excel', title: 'Danh sách câu hỏi' },
      { extend: 'print', text: '🖨️ In bảng', title: 'Danh sách câu hỏi' }
    ],
    pageLength: 20,
    language: {
      search: "🔍 Tìm kiếm:", lengthMenu: "Hiển thị _MENU_ dòng",
      info: "Hiển thị _START_ đến _END_ trong _TOTAL_ dòng",
      zeroRecords: "Không tìm thấy kết quả phù hợp", infoEmpty: "Không có dữ liệu",
      paginate: { first: "«", last: "»", next: "›", previous: "‹" }
    },
    order: [[0, 'desc']]
  });

  $('#questionTable tbody').on('click', 'tr', function () {
    const tds = $(this).find("td");
    const data = {
      id: tds.eq(0).text().trim(), question: tds.eq(1).text().trim(),
      answer1: tds.eq(2).text().trim(), answer2: tds.eq(3).text().trim(),
      answer3: tds.eq(4).text().trim(), answer4: tds.eq(5).text().trim(),
      correct_answer: tds.eq(6).text().trim(), topic: tds.eq(7).text().trim(),
      image: tds.eq(8).find('img').attr('src') || ""
    };
    currentRowIndex = table.row(this, { search: 'applied' }).index();
    selectRow(this, data);
  });

  document.getElementById("filterTopicInline").addEventListener("change", function () {
    const topic = this.value;
    location.href = topic ? `get_question.php?topic=${encodeURIComponent(topic)}` : "get_question.php";
  });

  document.getElementById("excelInput").addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (evt) {
      const workbook = XLSX.read(evt.target.result, { type: "binary" });
      const sheet = workbook.Sheets[workbook.SheetNames[0]];
      const rawRows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

      const rows = rawRows.filter(row => row.length >= 8 && row[0] !== "ID");
      const formatted = rows.map(r => ({
        question: r[1] || '', answer1: r[2] || '', answer2: r[3] || '', answer3: r[4] || '',
        answer4: r[5] || '', correct_answer: r[6] || '', topic: r[7] || '', image: r[8] || ''
      }));

      // Gửi lên server lưu vào CSDL
      $.post("get_question.php", { excelData: JSON.stringify(formatted) })
      .done(res => {
      if (res.trim() === "OK") location.reload();
      else alert("Lỗi khi lưu dữ liệu:\n" + res);
      })
      .fail(err => alert("Không kết nối được đến máy chủ."));
    };
    reader.readAsBinaryString(file);
  });
});
</script>
</body>
</html>
