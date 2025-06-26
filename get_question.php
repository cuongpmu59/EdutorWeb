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

  <!-- jQuery + DataTables -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- DataTables Buttons -->
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

  <!-- MathJax -->
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 10px;
      margin: 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 6px 8px;
      text-align: left;
      vertical-align: top;
    }
    tr:hover {
      background-color: #f1f9ff;
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
      border: 1px solid #aaa;
      border-radius: 4px;
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
        <th style="width: 40px;">ID</th>
        <th>Câu hỏi</th>
        <th>Đáp án A</th>
        <th>Đáp án B</th>
        <th>Đáp án C</th>
        <th>Đáp án D</th>
        <th style="width: 80px;">Đáp án đúng</th>
        <th style="width: 100px;">Chủ đề</th>
        <th style="width: 50px;">Ảnh</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($rows) > 0): ?>
        <?php foreach ($rows as $row): ?>
          <?php
            $thumbUrl = '';
            if (!empty($row["image"])) {
              if (strpos($row["image"], 'cloudinary') !== false) {
                // Tạo ảnh thumbnail Cloudinary
                $thumbUrl = preg_replace('~upload/~', 'upload/w_60,h_60,c_fill/', $row["image"]);
              } else {
                // Dùng ảnh nội bộ (nếu có)
                $thumbUrl = 'https://cuongedutor.infy.uk/images/uploads/' . ltrim($row["image"], "/");
              }
            }
          ?>
          <tr onclick='selectRow(this, <?= json_encode([
              "id"             => $row["id"],
              "question"       => $row["question"],
              "answer1"        => $row["answer1"],
              "answer2"        => $row["answer2"],
              "answer3"        => $row["answer3"],
              "answer4"        => $row["answer4"],
              "correct_answer" => strtoupper(trim($row["correct_answer"])),
              "topic"          => $row["topic"] ?? "",
              "image"          => $row["image"] ?? ""
          ], JSON_UNESCAPED_UNICODE) ?>)'>
            <td><?= htmlspecialchars($row["id"]) ?></td>
            <td><?= htmlspecialchars($row["question"]) ?></td>
            <td><?= htmlspecialchars($row["answer1"]) ?></td>
            <td><?= htmlspecialchars($row["answer2"]) ?></td>
            <td><?= htmlspecialchars($row["answer3"]) ?></td>
            <td><?= htmlspecialchars($row["answer4"]) ?></td>
            <td style="text-align:center; font-weight:bold;">
              <?= strtoupper(substr($row["correct_answer"], 0, 1)) ?>
            </td>
            <td><?= htmlspecialchars($row["topic"] ?? "") ?></td>
            <td style="text-align:center;">
              <?php if ($thumbUrl): ?>
                <img class="thumb" src="<?= htmlspecialchars($thumbUrl) ?>" alt="Ảnh" />
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="9" style="text-align:center;">Không có dữ liệu</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <script>
    function selectRow(row, data) {
      if (window.currentRow) window.currentRow.classList.remove("selected-row");
      window.currentRow = row;
      row.classList.add("selected-row");
      parent.postMessage({ type: "fillForm", data: data }, "*");
    }

    $(document).ready(function () {
      const table = $('#questionTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        language: {
          search: "Tìm kiếm:",
          lengthMenu: "Hiển thị _MENU_ mục",
          info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
          infoEmpty: "Không có dữ liệu",
          zeroRecords: "Không tìm thấy kết quả phù hợp",
          paginate: {
            first: "Đầu",
            last: "Cuối",
            next: "Sau",
            previous: "Trước"
          },
          buttons: {
            copy: "Sao chép",
            csv: "Xuất CSV",
            excel: "Xuất Excel",
            pdf: "Xuất PDF",
            print: "In"
          }
        }
      });

      // Lọc theo chủ đề
      $('#filterTopic').on('change', function () {
        const topic = this.value;
        table.column(7).search(topic ? '^' + topic + '$' : '', true, false).draw();
      });

      // Render lại MathJax
      table.on('draw', () => MathJax.typesetPromise());

      // Tự chọn dòng đầu tiên sau khi tải
      setTimeout(() => {
        const firstRow = document.querySelector("tbody tr");
        if (firstRow) firstRow.click();
      }, 100);
    });
  </script>
</body>
</html>
