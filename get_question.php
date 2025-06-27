<?php
require 'db_connection.php';
header("X-Frame-Options: SAMEORIGIN");

try {
    $stmt = $conn->prepare("SELECT * FROM questions ORDER BY id DESC");
    $stmt->execute();
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 6px;
      vertical-align: top;
    }
    tr:hover {
      background: #f1f9ff;
      cursor: pointer;
    }
    .selected-row {
      background-color: #cceeff !important;
    }
    img.thumb {
      max-width: 40px;
      max-height: 40px;
      display: block;
      margin: auto;
      border-radius: 4px;
      border: 1px solid #aaa;
    }
    #filterTopic {
      margin-bottom: 10px;
      padding: 4px 8px;
    }
  </style>
</head>
<body>

  <label for="filterTopic"><strong>Lọc theo chủ đề:</strong></label>
  <select id="filterTopic">
    <option value="">-- Tất cả --</option>
    <?php
      $topics = array_unique(array_column($rows, 'topic'));
      sort($topics);
      foreach ($topics as $tp) {
        echo '<option value="' . htmlspecialchars($tp) . '">' . htmlspecialchars($tp) . '</option>';
      }
    ?>
  </select>

  <table id="questionTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Câu hỏi</th>
        <th>A</th>
        <th>B</th>
        <th>C</th>
        <th>D</th>
        <th>Đúng</th>
        <th>Chủ đề</th>
        <th>Ảnh</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $row): 
        $thumbUrl = '';
        if (!empty($row["image"])) {
          $thumbUrl = strpos($row["image"], 'cloudinary') !== false
            ? preg_replace('~upload/~', 'upload/w_60,h_60,c_fill/', $row["image"])
            : 'https://cuongedutor.infy.uk/images/uploads/' . ltrim($row["image"], "/");
        }
        $data = json_encode([
          "id" => $row["id"],
          "question" => $row["question"],
          "answer1" => $row["answer1"],
          "answer2" => $row["answer2"],
          "answer3" => $row["answer3"],
          "answer4" => $row["answer4"],
          "correct_answer" => strtoupper(trim($row["correct_answer"])),
          "topic" => $row["topic"],
          "image" => $row["image"]
        ], JSON_UNESCAPED_UNICODE);
      ?>
      <tr onclick='selectRow(this, <?= $data ?>)'>
        <td><?= htmlspecialchars($row["id"]) ?></td>
        <td><?= htmlspecialchars($row["question"]) ?></td>
        <td><?= htmlspecialchars($row["answer1"]) ?></td>
        <td><?= htmlspecialchars($row["answer2"]) ?></td>
        <td><?= htmlspecialchars($row["answer3"]) ?></td>
        <td><?= htmlspecialchars($row["answer4"]) ?></td>
        <td style="text-align:center; font-weight:bold;">
          <?= strtoupper(substr($row["correct_answer"], 0, 1)) ?>
        </td>
        <td><?= htmlspecialchars($row["topic"]) ?></td>
        <td style="text-align:center;">
          <?php if ($thumbUrl): ?>
            <img class="thumb" src="<?= htmlspecialchars($thumbUrl) ?>" alt="Ảnh" />
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <script>
    function selectRow(row, data) {
      if (window.currentRow) window.currentRow.classList.remove("selected-row");
      window.currentRow = row;
      row.classList.add("selected-row");
      row.scrollIntoView({ block: 'center', behavior: 'smooth' });
      parent.postMessage({ type: "fillForm", data }, "*");
    }

    $(document).ready(function () {
      const table = $('#questionTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        language: {
          search: "Tìm:",
          lengthMenu: "Hiển thị _MENU_ dòng",
          info: "Từ _START_ đến _END_ của _TOTAL_ dòng",
          infoEmpty: "Không có dữ liệu",
          zeroRecords: "Không tìm thấy kết quả phù hợp",
          paginate: { first: "Đầu", last: "Cuối", next: "→", previous: "←" }
        }
      });

      $('#filterTopic').on('change', function () {
        const topic = this.value;
        table.column(7).search(topic ? '^' + topic + '$' : '', true, false).draw();
      });

      // Chọn dòng đầu tiên sau khi tải
      setTimeout(() => {
        const firstRow = document.querySelector("tbody tr");
        if (firstRow) firstRow.click();
      }, 200);

      // Render lại MathJax nếu cần
      table.on('draw', () => MathJax.typesetPromise());
    });

    // Điều hướng ↑ / ↓
    document.addEventListener("keydown", function (e) {
      const selected = document.querySelector("tbody tr.selected-row");
      if (!selected) return;

      let targetRow;
      if (e.key === "ArrowDown") {
        targetRow = selected.nextElementSibling;
      } else if (e.key === "ArrowUp") {
        targetRow = selected.previousElementSibling;
      }

      if (targetRow) {
        targetRow.click();
        e.preventDefault();
      }
    });
  </script>
</body>
</html>
