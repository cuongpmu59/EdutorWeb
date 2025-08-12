<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>
  <script>
  window.MathJax = {
    tex: {inlineMath: [['$', '$'], ['\\(', '\\)']]},
    svg: {fontCache: 'global'}
  };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>
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
        <th>A</th>
        <th>B</th>
        <th>C</th>
        <th>D</th>
        <th>Đáp án</th>
        <th>Hình</th>
      </tr>
    </thead>
  </table>

  <!-- JS thư viện -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- Khởi tạo DataTable với server-side processing -->
  <script>
  $(document).ready(function(){
    $('#mcTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: '../../includes/mc/mc_table_data.php',
        type: 'POST'
      },
      columns: [
        { data: 'mc_id' },
        { data: 'mc_topic' },
        { data: 'mc_question' },
        { data: 'mc_answer1' },
        { data: 'mc_answer2' },
        { data: 'mc_answer3' },
        { data: 'mc_answer4' },
        { data: 'mc_correct_answer' },
        { data: 'mc_image_url', render: function(data){
            return data ? `<img src="${data}" width="50">` : '';
          }
        }
      ],
      pageLength: 20,
      order: [[0, 'desc']],
      drawCallback: function(){
        if (window.MathJax) {
          MathJax.typesetPromise();
        }
      }
    });
  });
  </script>

</body>
</html>
