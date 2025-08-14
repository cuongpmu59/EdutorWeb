<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý câu hỏi</title>

  <!-- MathJax -->
  <script>
    window.MathJax = {
      tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
      svg: { fontCache: 'global' }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" async></script>

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

  <style>
    body { font-family: Arial, sans-serif; padding: 16px; }
    table img {
      border-radius: 4px;
      object-fit: cover;
      max-width: 80px;
      max-height: 80px;
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
        <th>Hình minh họa</th>
      </tr>
    </thead>
  </table>

  <!-- jQuery + DataTables -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <script>
$(function () {
  const table = $('#mcTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '../../includes/mc/mc_fetch_data.php',
      type: 'POST',
      error: function(xhr) {
        console.error('AJAX error:', xhr.status, xhr.responseText);
        alert('Không thể tải dữ liệu (HTTP ' + xhr.status + '). Xem console để biết chi tiết.');
      }
    },
    order: [[0, 'desc']],
    columns: [
      { data: 'mc_id' },
      { data: 'mc_topic' },
      { data: 'mc_question' },
      { data: 'mc_answer1' },
      { data: 'mc_answer2' },
      { data: 'mc_answer3' },
      { data: 'mc_answer4' },
      { data: 'mc_correct_answer' },
      {
        data: 'mc_image_url',
        render: function(data) {
          return data ? '<img src="' + data + '" alt="ảnh">' : '';
        }
      }
    ],
    language: {
      processing: 'Đang tải...',
      search: 'Tìm:',
      lengthMenu: 'Hiển thị _MENU_ dòng',
      info: 'Hiển thị _START_–_END_ / _TOTAL_ dòng',
      infoEmpty: 'Không có dữ liệu',
      infoFiltered: '(lọc từ _MAX_ dòng)',
      paginate: { first: 'Đầu', last: 'Cuối', next: 'Sau', previous: 'Trước' },
      zeroRecords: 'Không tìm thấy kết quả phù hợp'
    },
    responsive: true
  });

  // 🆕 Sự kiện click vào dòng để gửi dữ liệu sang mc_form.php
  $('#mcTable tbody').on('click', 'tr', function () {
    const rowData = $('#mcTable').DataTable().row(this).data();
    if (!rowData) return;

    // Gán ID vào hidden input
    document.getElementById('mc_id').value = rowData.mc_id;

    // Gán các trường khác
    document.getElementById('mc_question').value = rowData.mc_question || '';
    document.getElementById('mc_answer1').value = rowData.mc_answer1 || '';
    document.getElementById('mc_answer2').value = rowData.mc_answer2 || '';
    document.getElementById('mc_answer3').value = rowData.mc_answer3 || '';
    document.getElementById('mc_answer4').value = rowData.mc_answer4 || '';
    document.getElementById('mc_correct_answer').value = rowData.mc_correct_answer || '';

    // Hiển thị ảnh nếu có
    const imgPreview = document.getElementById('mc_image_preview');
    if (rowData.mc_image_url) {
        imgPreview.src = rowData.mc_image_url;
        imgPreview.style.display = 'block';
    } else {
        imgPreview.src = '';
        imgPreview.style.display = 'none';
    }
});
</script>

  <script src="../../js/mc/mc_table_arrow_key.js"></script>
</body>
</html>
