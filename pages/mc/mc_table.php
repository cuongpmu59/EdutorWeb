<!-- mc_table.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách câu hỏi</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <style>
    table img {
      border-radius: 4px;
      object-fit: cover;
    }
  </style>
</head>
<body>

  <h2>📋 Danh sách câu hỏi trắc nghiệm</h2>
  <table id="mcTable" class="display" style="width:100%">
    <thead>
      <tr>
        <th>ID</th>
        <th>Chủ đề</th>
        <th>Câu hỏi</th>
        <th>Đáp án 1</th>
        <th>Đáp án 2</th>
        <th>Đáp án 3</th>
        <th>Đáp án 4</th>
        <th>Đúng</th>
        <th>Ảnh</th>
      </tr>
    </thead>
  </table>

  <!-- jQuery + DataTables -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- Khởi tạo bảng -->
  <script>
    $(document).ready(function () {
      $('#mcTable').DataTable({
        ajax: 'mc_questions.php',
        columns: [
          { data: 'mc_id' },
          { data: 'mc_topic' },
          { data: 'mc_question' },
          { data: 'mc_answer1' },
          { data: 'mc_answer2' },
          { data: 'mc_answer3' },
          { data: 'mc_answer4' },
          { data: 'mc_correct_anwer' },
          {
            data: 'mc_image_url',
            render: function (data) {
              if (!data) return '';
              const thumb = data.replace('/upload/', '/upload/w_50,h_50,c_fill/');
              return `<img src="${thumb}" alt="ảnh" width="50" height="50">`;
            },
            orderable: false,
            searchable: false
          }
        ],
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
        }
      });
    });
  </script>

</body>
</html>
