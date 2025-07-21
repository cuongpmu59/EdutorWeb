$(document).ready(function () {
  const table = $('#mcTable').DataTable({
    scrollX: true,
    dom: '<"top-controls"Bf>rtip',
    fixedHeader: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    buttons: [
      {
        extend: 'excelHtml5',
        text: '⬇️ Xuất Excel',
        title: 'mc_questions',
        exportOptions: { columns: ':visible' }
      },
      {
        extend: 'print',
        text: '🖨️ In bảng',
        exportOptions: { columns: ':visible' }
      },
      {
        text: '📥 Nhập Excel',
        action: function () { $('#excelFile').click(); }
      }
    ]
  });

  // Bộ lọc chủ đề và ô tìm kiếm
  $('#mcTable_filter').html(`
    <div class="filter-left">
      📚 Chủ đề:
      <select id="filter-topic">
        <option value="">-- Tất cả --</option>
      </select>
    </div>
    <div class="filter-right">
      🔍 Tìm kiếm: <input type="search" class="form-control input-sm" placeholder="">
    </div>
  `);

  // Load chủ đề từ PHP
  $.get('includes/mc_filter.php', function (options) {
    $('#filter-topic').append(options);
  });

  // Lọc theo chủ đề
  $('#filter-topic').on('change', function () {
    table.column(1).search(this.value).draw();
  });

  // Tìm kiếm tổng
  $('#mcTable_filter input[type="search"]').on('keyup change', function () {
    table.search(this.value).draw();
  });

  // Hỗ trợ tìm kiếm không dấu
  $.fn.dataTable.ext.type.search.string = function (data) {
    return !data ? '' : data.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
  };

  // Sau khi vẽ bảng: cập nhật MathJax và tự chọn dòng đầu
  table.on('draw', function () {
    if (window.MathJax) MathJax.typesetPromise();

    if (!table.row('.selected').node()) {
      const firstRow = table.row(0);
      if (firstRow.node()) {
        $(firstRow.node()).addClass('selected');
        sendRowData(firstRow);
      }
    }
  });

  // Xem ảnh
  $(document).on('click', '.thumb', function () {
    $('#imgModalContent').attr('src', $(this).attr('src'));
    $('#imgModal').fadeIn();
  });
  $('#imgModal').on('click', function () {
    $(this).fadeOut();
  });

  // Gửi dữ liệu dòng về form cha
  function sendRowData(row) {
    const data = row.data();
    if (!data) return;
  
    const $row = row.node();
    const cells = $($row).find('td');
  
    const message = {
      type: "mc_select_row",
      data: {
        id: cells.eq(0).data('raw'),
        topic: cells.eq(1).data('raw'),
        question: cells.eq(2).data('raw'),
        answer1: cells.eq(3).data('raw'),
        answer2: cells.eq(4).data('raw'),
        answer3: cells.eq(5).data('raw'),
        answer4: cells.eq(6).data('raw'),
        correct: cells.eq(7).data('raw'),
        image: cells.eq(8).find('img').attr('src') || ''
      }
    };
  
    window.parent.postMessage(message, "*");
  }
  

  // Click chọn dòng
  $('#mcTable tbody').on('click', 'tr', function () {
    table.$('tr.selected').removeClass('selected');
    $(this).hide().addClass('selected').fadeIn(200)[0].scrollIntoView({
      behavior: 'smooth',
      block: 'center'
    });
    sendRowData(table.row(this));
  });
  

  // Điều hướng bằng bàn phím
$(document).on('keydown', function (e) {
  const selected = table.row('.selected');
  if (!selected.node()) return;
  let index = selected.index();

  if (e.key === 'ArrowUp' && index > 0) index--;
  else if (e.key === 'ArrowDown' && index < table.rows().count() - 1) index++;
  else return;

  e.preventDefault();
  table.$('tr.selected').removeClass('selected');
  const nextRow = table.row(index);
  $(nextRow.node()).hide().addClass('selected').fadeIn(200)[0].scrollIntoView({
    behavior: 'smooth',
    block: 'center'
  });
  sendRowData(nextRow);
});

    

  // Nhập Excel
  $('#excelFile').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const worksheet = workbook.Sheets[workbook.SheetNames[0]];
      const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: '' });

      if (jsonData.length === 0) {
        alert("❌ File Excel rỗng hoặc không hợp lệ.");
        return;
      }

      $.ajax({
        url: '../../includes/mc_import_excel.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(jsonData),
        success: function (res) {
          alert("✅ Đã nhập " + res.inserted + " câu hỏi!");
          location.reload();
        },
        error: function () {
          alert("❌ Lỗi khi nhập file Excel.");
        }
      });
    };
    reader.readAsArrayBuffer(file);
  });
});
