<?php
require 'db_connection.php';

$sql = "SELECT * FROM questions ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Danh sách câu hỏi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: left;
        }

        tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        .selected-row {
            background-color: #d0f0ff !important;
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

            // Gửi dữ liệu câu hỏi về window cha (form)
            parent.postMessage(data, window.location.origin);
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
    </script>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Câu hỏi</th>
                <th>Đáp án A</th>
                <th>Đáp án B</th>
                <th>Đáp án C</th>
                <th>Đáp án D</th>
                <th>Đáp án đúng</th>
                <th>Ảnh</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr onclick='selectRow(this, <?php echo json_encode([
                    "id" => $row["id"],
                    "question" => $row["question"],
                    "answer1" => $row["answer1"],
                    "answer2" => $row["answer2"],
                    "answer3" => $row["answer3"],
                    "answer4" => $row["answer4"],
                    "correct_answer" => $row["correct_answer"],
                    "image" => $row["image"] ? "https://cuongedutor.infy.uk/images/" . $row["image"] : ""
                ]); ?>)'>
                    <td><?= $row["id"] ?></td>
                    <td><?= htmlspecialchars($row["question"]) ?></td>
                    <td><?= htmlspecialchars($row["answer1"]) ?></td>
                    <td><?= htmlspecialchars($row["answer2"]) ?></td>
                    <td><?= htmlspecialchars($row["answer3"]) ?></td>
                    <td><?= htmlspecialchars($row["answer4"]) ?></td>
                    <td><?= strtoupper(substr($row["correct_answer"], -1)) ?></td>
                    <td>
                        <?php if ($row["image"]): ?>
                            <img src="https://cuongedutor.infy.uk/images/<?= htmlspecialchars($row["image"]) ?>" width="40" alt="Image" />
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
    window.addEventListener("message", function(event) {
        const data = event.data;
        if (!data || typeof data !== "object") return;

        // Gán dữ liệu vào form
        document.getElementById("question_id").value = data.id || "";
        document.getElementById("question").value = data.question || "";
        document.getElementById("answer1").value = data.answer1 || "";
        document.getElementById("answer2").value = data.answer2 || "";
        document.getElementById("answer3").value = data.answer3 || "";
        document.getElementById("answer4").value = data.answer4 || "";
        document.getElementById("correct_answer").value = data.correct_answer || "A";

        // Cập nhật MathJax preview
        const previewDiv = document.getElementById("preview");
        previewDiv.innerHTML = data.question || "";
        if (window.MathJax) MathJax.typesetPromise([previewDiv]);

        // Hiển thị ảnh nếu có
        const imagePreview = document.getElementById("imagePreview");
        if (data.image) {
            imagePreview.src = data.image;
            imagePreview.style.display = "block";
        } else {
            imagePreview.src = "";
            imagePreview.style.display = "none";
        }
    });

    // Ẩn/hiện bảng câu hỏi
    document.getElementById("toggleTableBtn").addEventListener("click", function () {
        const iframe = document.getElementById("questionIframe");
        if (iframe.style.display === "none") {
            iframe.style.display = "block";
            this.textContent = "Ẩn bảng câu hỏi";
        } else {
            iframe.style.display = "none";
            this.textContent = "Hiện bảng câu hỏi";
        }
    });
</script>

</body>
</html>
