<?php
require 'db_connection.php';

$sql = "SELECT * FROM questions ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách câu hỏi</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 14px;
      padding: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      vertical-align: top;
    }
    th {
      background-color: #f2f2f2;
    }
    tr:hover {
      background-color: #eef;
      cursor: pointer;
    }
    img.thumb {
      max-width: 100px;
      max-height: 60px;
    }
  </style>
  <!-- Tích hợp MathJax nếu có công thức LaTeX -->
  <script>
    window.MathJax = {
      tex: {inlineMath: [['$', '$'], ['\\(', '\\)']]},
      svg: {fontCache: 'global'}
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body>
  <h3>Danh sách câu hỏi</h3>
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
    <th>Xoá</th> <!-- Cột mới -->
  </tr>
</thead>
<tbody>
  <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr onclick="selectRow(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)">
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['question']) ?></td>
      <td><?= htmlspecialchars($row['answer1']) ?></td>
      <td><?= htmlspecialchars($row['answer2']) ?></td>
      <td><?= htmlspecialchars($row['answer3']) ?></td>
      <td><?= htmlspecialchars($row['answer4']) ?></td>
      <td><?= htmlspecialchars($row['correct_answer']) ?></td>
      <td>
        <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
          <img class="thumb" src="<?= $row['image'] ?>" alt="Ảnh">
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
      <td>
        <button onclick="deleteRow(event, <?= $row['id'] ?>)">Xoá</button>
      </td>
    </tr>
  <?php endwhile; ?>
</tbody>

  </table>

  <script>
  function selectRow(data) {
    // Gửi dữ liệu về cửa sổ cha (form chính)
    window.parent.postMessage({ type: 'selectQuestion', data }, '*');
  }

  function deleteRow(event, id) {
    event.stopPropagation(); // Ngăn không gọi selectRow khi bấm nút

    if (!confirm("Bạn có chắc muốn xoá câu hỏi ID " + id + " không?")) return;

    fetch('question_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=delete&id=${id}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert("Đã xoá thành công.");
        location.reload(); // Tải lại trang để cập nhật bảng
      } else {
        alert("Xoá thất bại: " + (data.message || "Lỗi không rõ."));
      }
    })
    .catch(() => alert("Lỗi kết nối máy chủ."));
  }

  // Render lại MathJax sau khi tải
  window.onload = () => {
    if (window.MathJax) {
      MathJax.typesetPromise();
    }
  };
</script>
</body>
</html>

<?php mysqli_close($conn); ?>
