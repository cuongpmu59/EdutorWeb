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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Danh sách câu hỏi</title>
  <meta name="description" content="Quản lý và xem danh sách câu hỏi trắc nghiệm với hỗ trợ tìm kiếm, phân trang, và hiển thị công thức toán học.">

  <!-- jQuery (bắt buộc cho DataTables) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- MathJax (hiển thị công thức Toán) -->
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

  <!-- CSS riêng của bạn -->
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
                                <img class="thumb" src="https://cuongedutor.infy.uk/images/uploads/<?= htmlspecialchars(ltrim($row["image"], "/")) ?>" alt="Ảnh minh họa" />
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
