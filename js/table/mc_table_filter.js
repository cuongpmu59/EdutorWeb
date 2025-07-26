$(document).ready(function () {
    const table = $('#mcTable').DataTable();
  
    // Load danh sách chủ đề từ server
    $.get('../../includes/mc_filter.php', function (html) {
      $('#topicFilter').append(html);
    });
  
    // Lọc theo chủ đề (cột 1 là chủ đề)
    $('#topicFilter').on('change', function () {
      const val = $(this).val();
      table.column(1).search(val).draw();
    });
  
    // Lọc theo ô tìm kiếm tổng quát
    $('#tableSearch').on('input', function () {
      table.search(this.value).draw();
    });
  });
  