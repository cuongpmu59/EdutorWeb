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

    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

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
            cursor: zoom-in;
        }
        /* Modal phóng to ảnh */
        #imageModal {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            justify-content: center;
            align-items: center;
        }
        #imageModal img {
            max-width: 90%;
            max-height: 90%;
        }
        #imageModal span {
            position: absolute;
            top: 10px;
            right: 20px;
            color: white;
            font-size: 28px;
            cursor: pointer;
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

        function showImage(src) {
            document.getElementById("modalImage").src = src;
            document.getElementById("imageModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("imageModal").style.display = "none";
        }

        window.addEventListener("keydown", e => {
            if (e.key === "Escape") closeModal();
        });
    </script>
</head>
<body>
<div style="margin:10px 0;">
    <a href="export_pdf.php" target="_blank" class="btn btn-danger">📄 Xuất PDF</a>
</div>

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
                        $imageUrl = !empty($row["image"]) ? $row["image"] : "";
                        // Tạo URL thumbnail 40x40 từ Cloudinary nếu có
                        $thumbnailUrl = preg_replace("/upload\//", "upload/c_fill,h_40,w_40/", $imageUrl);
                    ?>
                    <tr tabindex="0" onclick='selectRow(this, <?php echo json_encode([
                        "id" => $row["id"],
                        "question" => $row["question"],
                        "answer1" => $row["answer1"],
                        "answer2" => $row["answer2"],
                        "answer3" => $row["answer3"],
                        "answer4" => $row["answer4"],
                        "correct_answer" => strtoupper(trim($row["correct_answer"])),
                        "topic" => $row["topic"] ?? "",
                        "image" => $imageUrl
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
                            <?php if (!empty($imageUrl)): ?>
                                <img class="thumb" src="<?= htmlspecialchars($thumbnailUrl) ?>" alt="Ảnh minh họa"
                                     onclick="showImage('<?= htmlspecialchars($imageUrl) ?>')">
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align:center;">Không có dữ liệu</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal phóng to ảnh -->
    <div id="imageModal">
        <span onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" />
    </div>
</body>
</html>
