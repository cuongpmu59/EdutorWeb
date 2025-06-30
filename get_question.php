<?php
require 'db_connection.php';
function wrapMath($text) {
    $trimmed = trim($text);
    if (preg_match('/(\\\\\(.+?\\\\\))|(\\\\\[(.+?)\\\\\])|(\$\$.+?\$\$)|(\$.+?\$)/', $trimmed)) {
        return $trimmed;
    }
    return '\\(' . htmlspecialchars($trimmed, ENT_QUOTES) . '\\)';
}

header("X-Frame-Options: SAMEORIGIN");

// ===== Lấy danh sách chủ đề duy nhất =====
$topics = [];
try {
    $stmtTopics = $conn->query("SELECT DISTINCT topic FROM questions WHERE topic IS NOT NULL AND topic != '' ORDER BY topic");
    $topics = $stmtTopics->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $topics = [];
}

try {
    $topicFilter = $_GET['topic'] ?? '';

    if ($topicFilter !== '') {
        $stmt = $conn->prepare("SELECT * FROM questions WHERE topic = :topic ORDER BY id DESC");
        $stmt->execute(['topic' => $topicFilter]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM questions ORDER BY id DESC");
        $stmt->execute();
    }
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
        thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: linear-gradient(to right, #007bff, #3399ff);
            color: white;
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
</head>
<body>

<div style="margin:10px 0;">
    <label for="filterTopicInline" style="margin-left: 15px;"><strong>Lọc theo chủ đề:</strong></label>
    <select id="filterTopicInline">
        <option value="">-- Tất cả --</option>
        <?php foreach ($topics as $t): ?>
            <option value="<?= htmlspecialchars($t) ?>" <?= ($topicFilter == $t ? 'selected' : '') ?>>
                <?= htmlspecialchars($t) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<script>
document.getElementById("filterTopicInline").addEventListener("change", function () {
    const topic = this.value;
    const newUrl = topic ? `get_question.php?topic=${encodeURIComponent(topic)}` : "get_question.php";
    window.location.href = newUrl;
});
</script>

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
                    $imageUrl = $row["image"] ?? "";
                    $thumbnailUrl = $imageUrl ? preg_replace("/upload\//", "upload/c_fill,h_40,w_40/", $imageUrl) : "";
                ?>
                <tr tabindex="0" onclick='selectRow(this, <?= json_encode([
                    "id" => $row["id"],
                    "question" => $row["question"],
                    "answer1" => $row["answer1"],
                    "answer2" => $row["answer2"],
                    "answer3" => $row["answer3"],
                    "answer4" => $row["answer4"],
                    "correct_answer" => strtoupper(trim($row["correct_answer"])),
                    "topic" => $row["topic"] ?? "",
                    "image" => $imageUrl
                ]) ?>)'>
                    <td><?= htmlspecialchars($row["id"]) ?></td>
                    <td><?= wrapMath($row["question"]) ?></td>
                    <td><?= wrapMath($row["answer1"]) ?></td>
                    <td><?= wrapMath($row["answer2"]) ?></td>
                    <td><?= wrapMath($row["answer3"]) ?></td>
                    <td><?= wrapMath($row["answer4"]) ?></td>

                    <td style="text-align:center; font-weight:bold;">
                        <?= strtoupper(substr($row["correct_answer"], 0, 1)) ?>
                    </td>
                    <td><?= htmlspecialchars($row["topic"] ?? "") ?></td>
                    <td style="text-align:center;">
                        <?php if ($imageUrl): ?>
                            <img class="thumb" src="<?= htmlspecialchars($thumbnailUrl) ?>" alt="Ảnh"
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

<!-- Modal ảnh -->
<div id="imageModal">
    <span onclick="closeModal()">&times;</span>
    <img id="modalImage" src="" />
</div>

<script>
let currentRow = null;

function selectRow(row, data) {
    if (currentRow) currentRow.classList.remove("selected-row");
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

window.onload = () => {
    MathJax.typesetPromise().then(() => {
        const firstRow = document.querySelector("tbody tr");
        if (firstRow) firstRow.click();
    });
};
</script>

</body>
</html>
