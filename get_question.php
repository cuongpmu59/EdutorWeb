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
            padding: 5px;
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
            border: 1px solid #aaa;
            border-radius: 3px;
            display: block;
            margin: auto;
        }
    </style>
    <script>
        let currentRow = null;

        function selectRow(row, data) {
            if (currentRow) currentRow.classList.remove("selected-row");
            currentRow = row;
            row.classList.add("selected-row");

            // Gửi dữ liệu sang form cha
            parent.postMessage({ type: "fillForm", data: data }, "*");
        }

        function rowKeyNavigation(e) {
            const rows = document.querySelectorAll("tbody tr");
            if (!rows.length) return;

            let index = currentRow ? Array.from(rows).indexOf(currentRow) : -1;

            if (e.key === "ArrowDown" && index < rows.length - 1) {
                if (currentRow) currentRow.classList.remove("selected-row");
                currentRow = rows[++index];
            } else if (e.key === "ArrowUp" && index > 0) {
                currentRow.classList.remove("selected-row");
                currentRow = rows[--index];
            } else return;

            currentRow.classList.add("selected-row");
            currentRow.click();
        }

        window.addEventListener("keydown", rowKeyNavigation);

        window.onload = () => {
            MathJax.typesetPromise();
        };
    </script>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th style="width:40px;">ID</th>
                <th>Chủ đề</th>
                <th>Câu hỏi</th>
                <th>A</th>
                <th>B</th>
                <th>C</th>
                <th>D</th>
                <th style="width:80px;">Đáp án đúng</th>
                <th style="width:50px;">Ảnh</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($rows): ?>
                <?php foreach ($rows as $row): ?>
                    <tr onclick='selectRow(this, <?= json_encode([
                        "id" => $row["id"],
                        "question" => $row["question"],
                        "answer1" => $row["answer1"],
                        "answer2" => $row["answer2"],
                        "answer3" => $row["answer3"],
                        "answer4" => $row["answer4"],
                        "correct_answer" => strtoupper(trim($row["correct_answer"])),
                        "image" => $row["image"],
                        "topic" => $row["topic"] ?? ""
                    ], JSON_UNESCAPED_UNICODE) ?>)'>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["topic"] ?? '') ?></td>
                        <td><?= htmlspecialchars($row["question"]) ?></td>
                        <td><?= htmlspecialchars($row["answer1"]) ?></td>
                        <td><?= htmlspecialchars($row["answer2"]) ?></td>
                        <td><?= htmlspecialchars($row["answer3"]) ?></td>
                        <td><?= htmlspecialchars($row["answer4"]) ?></td>
                        <td style="text-align:center; font-weight:bold;">
                            <?= strtoupper(substr($row["correct_answer"], 0, 1)) ?>
                        </td>
                        <td style="text-align:center;">
                            <?php if (!empty($row["image"])): ?>
                                <img class="thumb" src="<?= htmlspecialchars($row["image"]) ?>" alt="Ảnh" />
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align:center;">Không có dữ liệu</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
