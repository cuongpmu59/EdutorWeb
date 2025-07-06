<?php
require 'db_connection.php';
header("Content-Type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📋 Danh sách câu hỏi đúng/sai</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }
    table {
      font-size: 14px;
    }
    .preview-img {
      max-width: 100px;
      cursor: pointer;
      transition: 0.2s;
    }
    .preview-img:hover {
      transform: scale(1.2);
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      padding-top: 60px;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.8);
    }
    .modal-content {
      margin: auto;
      display: block;
      max-width: 90%;
    }
    .modal-content, .modal {
      animation-name: fadeIn;
      animation-duration: 0.3s;
    }
    @keyframes fadeIn {
      from {opacity: 0;} 
      to {opacity: 1;}
    }
    .close {
      position: absolute;
      top: 20px;
      right: 30px;
      color: white;
      font-size: 35px;
      font-weight: bold;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <h2>📋 Danh sách câu hỏi đúng/sai</h2>
  <table id="questionTable" class="display nowrap" style="width:100%">
    <thead>
      <tr>
        <th>#</th>
        <th>Chủ đề</th>
        <th>Đề bài chính</th>
        <th>Ý 1</th>
        <th>Ý 2</th>
        <th>Ý 3</th>
        <th>Ý 4</th>
        <th>Ảnh</th>
        <th>Thời gian</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $stmt = $conn->prepare("SELECT * FROM true_false_questions ORDER BY id DESC");
      $stmt->execute();
      $result = $stmt->get_result();
      while ($row = $result->fetch_assoc()):
      ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['topic']) ?></td>
          <td><?= htmlspecialchars($row['main_question']) ?></td>
          <td>
            <?= htmlspecialchars($row['statement1']) ?><br>
            <?= $row['correct_answer1'] ? '✅ Đúng' : '❌ Sai' ?>
          </td>
          <td>
            <?= htmlspecialchars($row['statement2']) ?><br>
            <?= $row['correct_answer2'] ? '✅ Đúng' : '❌ Sai' ?>
          </td>
          <td>
            <?= htmlspecialchars($row['statement3']) ?><br>
            <?= $row['correct_answer3'] ? '✅ Đúng' : '❌ Sai' ?>
          </td>
          <td>
            <?= htmlspecialchars($row['statement4']) ?><br>
            <?= $row['correct_answer4'] ? '✅ Đúng' : '❌ Sai' ?>
          </td>
          <td>
            <?php if (!empty($row['image'])): ?>
              <img src="<?= $row['image'] ?>" class="preview-img" onclick="showModal(this.src)">
            <?php endif; ?>
          </td>
          <td><?= $row['created_at'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Modal hiển thị ảnh lớn -->
  <div id="imgModal" class="modal" onclick="this.style.display='none'">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#questionTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: ['excel', 'print'],
        language: {
          search: "🔍 Tìm:",
          lengthMenu: "Hiển thị _MENU_ mục",
          info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
          infoEmpty: "Không có dữ liệu",
          emptyTable: "Chưa có câu hỏi nào",
          paginate: {
            next: "Trang sau",
            previous: "Trang trước"
          }
        }
      });
    });

    function showModal(src) {
      const modal = document.getElementById("imgModal");
      const modalImg = document.getElementById("modalImage");
      modal.style.display = "block";
      modalImg.src = src;
    }
  </script>
</body>
</html>
