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
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0 5px;
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
            border-radius: 3px;
        }
    </style>

    <script>
        let currentRow = null;

        function selectRow(row, data) {
            if (currentRow) {
                currentRow.classList.remove("selected-row");
            }
            currentRow = row;
            row.classList.add("selected-row");

            parent.postMessage({ type: "fillForm", data: data }, window.location.origin);
        }

        function rowKeyNavigation(event) {
            const rows = document.querySelectorAll("tbody tr");
            if (rows.length === 0) return;

            if (!currentRow) {
                currentRow = rows[0];
                currentRow.classList.add("selected-row");
                currentRow.click();
                return;
            }

            let index = Array.from(rows).indexOf(currentRow);

            if (event.key === "ArrowDown" && index < rows.length - 1) {
                rows[index].classList.remove("selected-row");
                currentRow = rows[index + 1];
            } else if (event.key === "ArrowUp" && index > 0) {
                rows[index].classList.remove("selected-row");
                currentRow = rows[index - 1];
            } else {
                return;
            }

            currentRow.classList.add("selected-row");
            currentRow.click();
        }

        window.addEventListener("keydown", rowKeyNavigation);

        window.onload = () => {
            MathJax.typesetPromise().then(() => {
                const firstRow = document.querySelector("tbody tr");
                if (firstRow) firstRow.click();
            });
        };
    </script>
</head>
<body>
    <table>
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
                    <tr tabindex="0" onclick='selectRow(this, <?php echo json_encode([
                        "id" => $row["id"],
                        "question" => $row["question"],
                        "answer1" => $row["answer1"],
                        "answer2" => $row["answer2"],
                        "answer3" => $row["answer3"],
                        "answer4" => $row["answer4"],
                        "correct_answer" => strtoupper(trim($row["correct_answer"])),
                        "topic" => $row["topic"] ?? "",
                        "image" => $row["image"] ? "https://cuongedutor.infy.uk/images/uploads/" . ltrim($row["image"], "/") : ""
                    ]); ?>)'>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["question"]) ?></td>
                        <td><?= htmlspecialchars($row["answer1"]) ?></td>
                        <td><?= htmlspecialchars($row["answer2"]) ?></td>
                        <td><?= htmlspecialchars($row["answer3"]) ?></td>
                        <td><?= htmlspecialchars($row["answer4"]) ?></td>
                        <td style="text-align:center; font-weight:bold;"><?= strtoupper(substr($row["correct_answer"], 0, 1)) ?></td>
                        <td><?= htmlspecialchars($row["topic"] ?? "") ?></td>
                        <td style="text-align:center;">
                            <?php if (!empty($row["image"])): ?>
                              <img class="thumb" src="https://cuongedutor.infy.uk/images/uploads/<?= htmlspecialchars(ltrim($row["image"], "/")) ?>"
                                  alt="Ảnh minh họa" onclick="showImageModal(this.src)" />
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align:center;">Không có dữ liệu</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- 🌟 Modal Nhập Excel .xlsx -->
<div class="modal fade" id="xlsxModal" tabindex="-1" aria-labelledby="xlsxModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="xlsxUploadForm" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="xlsxModalLabel">📥 Nhập câu hỏi từ Excel (.xlsx)</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="xlsx_file" class="form-label">Chọn file Excel:</label>
          <input class="form-control" type="file" name="xlsx_file" id="xlsx_file" accept=".xlsx" required>
        </div>
        <div class="alert alert-info">
          <strong>Lưu ý:</strong> File cần đúng định dạng cột: <code>question</code>, <code>answer1</code>, <code>answer2</code>, <code>answer3</code>, <code>answer4</code>, <code>correct_answer</code>, <code>topic</code>, <code>image_url</code>.
        </div>
      </div>
      <div class="modal-footer">
        <a href="template.xlsx" class="btn btn-link" download>Tải mẫu Excel</a>
        <button type="submit" class="btn btn-success">Tải lên</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
