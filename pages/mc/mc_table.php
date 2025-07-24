<?php
require_once '../../db_connection.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Danh sách câu hỏi</title>
  <link rel="stylesheet" href="../../css/table_ui.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="mc-filter-container">
    <div class="search-box">
      <label>Tìm kiếm: <input type="text" id="mcSearchBox" placeholder="Nhập từ khóa..."></label>
    </div>
    <div class="filter-box">
      <label>Lọc theo chủ đề:
        <select id="mcTopicFilter">
          <option value="">Tất cả</option>
        </select>
      </label>
    </div>
  </div>

  <table id="mcTable" class="display" style="width:100%">
    <thead>
      <tr>
        <th>ID</th>
        <th>Chủ đề</th>
        <th>Câu hỏi</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

  <script>
    let table;

    $(document).ready(function () {
      table = $('#mcTable').DataTable({
        ajax: {
          url: '../../includes/mc_get_data.php',
          dataSrc: 'data'
        },
        columns: [
          { data: 'mc_id' },
          { data: 'mc_topic' },
          { data: 'mc_question' }
        ],
        dom: 'Bfrtip',
        buttons: ['excel'],
        pageLength: 10
      });

      // Tìm kiếm
      $('#mcSearchBox').on('keyup', function () {
        table.search(this.value).draw();
      });

      // Lọc chủ đề
      table.on('xhr', function () {
        const data = table.ajax.json().data;
        const topics = [...new Set(data.map(item => item.mc_topic))].sort();
        const $select = $('#mcTopicFilter');
        $select.empty().append('<option value="">Tất cả</option>');
        topics.forEach(topic => {
          $select.append(`<option value="${topic}">${topic}</option>`);
        });
      });

      $('#mcTopicFilter').on('change', function () {
        const val = $(this).val();
        table.column(1).search(val).draw();
      });

      // Gửi postMessage khi click vào 1 dòng
      $('#mcTable tbody').on('click', 'tr', function () {
        const rowData = table.row(this).data();
        if (window.parent && rowData && rowData.mc_id) {
          window.parent.postMessage({
            type: 'mc_select_row',
            data: { mc_id: rowData.mc_id }
          }, '*');
        }
      });
    });
  </script>
</body>
</html>
