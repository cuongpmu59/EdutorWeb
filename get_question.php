<?php
require 'db_connection.php';

// Cho phép hiển thị trong iframe cùng origin
header("X-Frame-Options: SAMEORIGIN");

// Lấy tất cả câu hỏi
try {
    $stmt = $conn->prepare("SELECT * FROM questions ORDER BY id DESC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $rows = [];
    // Bạn có thể log lỗi ở đây
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Danh sách câu hỏi</title>

    <!-- MathJax để hiển thị công thức LaTeX -->
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

            // Gửi dữ liệu sang parent (question_form.php)
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

        // Render MathJax sau khi trang load xong
        window.onload = () => {
            MathJax.typesetPromise();
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
                <th style="width: 50px;">Ảnh</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($rows) > 0): ?>
                <?php foreach ($rows as $row): ?>
                    <tr onclick='selectRow(this, <?php echo json_encode([
                        "id" => $row["id"],
                        "question" => $row["question"],
                        "answer1" => $row["answer1"],
                        "answer2" => $row["answer2"],
                        "answer3" => $row["answer3"],
                        "answer4" => $row["answer4"],
                        "correct_answer" => strtoupper(trim($row["correct_answer"])),
                        "image" => $row["image"] ? "https://cuongedutor.infy.uk/images/" . $row["image"] : ""
                    ]); ?>)'>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["question"]) ?></td>
                        <td><?= htmlspecialchars($row["answer1"]) ?></td>
                        <td><?= htmlspecialchars($row["answer2"]) ?></td>
                        <td><?= htmlspecialchars($row["answer3"]) ?></td>
                        <td><?= htmlspecialchars($row["answer4"]) ?></td>
                        <td style="text-align:center; font-weight:bold;"><?= strtoupper(substr($row["correct_answer"], 0, 1)) ?></td>
                        <td style="text-align:center;">
                            <?php if ($row["image"]): ?>
                                <img class="thumb" src="https://cuongedutor.infy.uk/images/<?= htmlspecialchars($row["image"]) ?>" alt="Ảnh minh họa" />
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center;">Không có dữ liệu</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
