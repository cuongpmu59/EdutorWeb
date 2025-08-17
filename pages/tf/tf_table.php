<?php
// tf_table.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi Đúng/Sai</title>

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

  <!-- CSS tùy chỉnh -->
  <link rel="stylesheet" href="../../css/tf/tf_table_layout.css">
  <link rel="stylesheet" href="../../css/tf/tf_table_toolbar.css">

  <!-- jQuery + DataTables -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

  <!-- MathJax -->
  <script>
    window.MathJax = {
      tex: { inlineMath: [['$', '$'], ['\\(', '\\)']], displayMath: [['\\[', '\\]'], ['$$','$$']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
</head>
<body>
  <h2>Danh sách câu hỏi Đúng/Sai</h2>
  <table id="tfTable" class="display nowrap" style="width:100%">
    <thead>
      <tr>
        <th>ID</th>
        <th>Chủ đề</th>
        <th>Câu hỏi</th>
        <th>Mệnh đề 1</th>
        <th>Đ/S 1</th>
        <th>Mệnh đề 2</th>
        <th>Đ/S 2</th>
        <th>Mệnh đề 3</th>
        <th>Đ/S 3</th>
        <th>Mệnh đề 4</th>
        <th>Đ/S 4</th>
        <th>Ảnh</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
      </tr>
    </thead>
  </table>

  <script>
  $(document).ready(function() {
    const table = $('#tfTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: '../../php/tf/tf_fetch_data.php',
      responsive: true,
      scrollX: true,
      dom: 'Bfrtip',
      buttons: [
        { extend: 'excel', text: 'Xuất Excel' },
        { extend: 'pdf', text: 'Xuất PDF' },
        { extend: 'print', text: 'In' }
      ],
      columns: [
        { data: 'tf_id' },
        { data: 'tf_topic' },
        { data: 'tf_question',
          render: function(data) { return data ? data.replace(/\n/g,'<br>') : ''; }
        },
        { data: 'tf_statement1' },
        { data: 'tf_correct_answer1',
          render: function(val) { return val == 1 ? 'Đúng' : 'Sai'; }
        },
        { data: 'tf_statement2' },
        { data: 'tf_correct_answer2',
          render: function(val) { return val == 1 ? 'Đúng' : 'Sai'; }
        },
        { data: 'tf_statement3' },
        { data: 'tf_correct_answer3',
          render: function(val) { return val == 1 ? 'Đúng' : 'Sai'; }
        },
        { data: 'tf_statement4' },
        { data: 'tf_correct_answer4',
          render: function(val) { return val == 1 ? 'Đúng' : 'Sai'; }
        },
        { data: 'tf_image_url',
          render: function(url) {
            return url ? '<img src="'+url+'" style="max-width:80px;max-height:80px;">' : '';
          }
        },
        { data: 'created_at' },
        { data: null, orderable: false,
          render: function(row) {
            return `<button class="fill-form" data-id="${row.tf_id}">Chọn</button>`;
          }
        }
      ],
      drawCallback: function() {
        MathJax.typesetPromise();
      }
    });

    // Gửi dữ liệu về form cha qua postMessage
    $('#tfTable').on('click', '.fill-form', function() {
      const data = table.row($(this).parents('tr')).data();
      window.parent.postMessage({ type: 'fill-form', data }, '*');
    });
  });
  </script>
</body>
</html>
