$(document).ready(function () {
    const table = $('#mcTable').DataTable({
      scrollX: true,
      fixedHeader: true,
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      dom: '<"top-controls"B>rtip',
      columnDefs: [
        {
          targets: -1, // Cột ảnh cuối cùng
          className: 'dt-center',
          orderable: false,
          searchable: false
        }
      ],
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
          action: function () {
            $('#excelFile').click();
          }
        }
      ]
    });
  
    // Normalize tiếng Việt không dấu cho tìm kiếm
    $.fn.dataTable.ext.type.search.string = function (data) {
      return !data ? '' : data.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    };
  
    // Tìm kiếm tùy chỉnh
    $('#customSearch').on('keyup change', function () {
      table.search(this.value).draw();
    });
  
    // Lọc theo chủ đề (cột 1)
    $('#topicFilter').on('change', function () {
      const selected = this.value;
      table.column(1).search(selected ? '^' + selected + '$' : '', true, false).draw();
    });
  
    // Vẽ lại MathJax sau mỗi lần cập nhật bảng
    table.on('draw', function () {
      if (window.MathJax) MathJax.typesetPromise();
    });
  
    // Phóng to ảnh khi click
    $(document).on('click', '.thumb', function () {
      const imgSrc = $(this).attr('src');
      if (imgSrc) {
        $('#imgModalContent').attr('src', imgSrc);
        $('#imgModal').fadeIn();
      }
    });
  
    // Đóng modal ảnh khi click ra ngoài
    $('#imgModal').on('click', function (e) {
      if (!$(e.target).is('#imgModalContent')) {
        $('#imgModal').fadeOut();
      }
    });
  
    // Gửi dữ liệu dòng về form cha
    function sendRowData(row) {
      const $cells = $(row.node()).find('td');
      const getRaw = i => $cells.eq(i).data('raw') || '';
      const data = {
        id: getRaw(0),
        topic: getRaw(1),
        question: getRaw(2),
        answer1: getRaw(3),
        answer2: getRaw(4),
        answer3: getRaw(5),
        answer4: getRaw(6),
        correct: getRaw(7),
        image: $cells.eq(8).find('img.thumb').attr('src') || ''
      };
      window.parent.postMessage({ type: 'mc_select_row', data }, '*');
      if (window.MathJax) MathJax.typesetPromise();
    }
  
    // Chọn dòng bằng chuột
    $('#mcTable tbody').on('click', 'tr', function () {
      table.$('tr.selected').removeClass('selected');
      $(this).addClass('selected');
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
      $(nextRow.node()).addClass('selected')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
      sendRowData(nextRow);
    });
  
    // Xử lý nhập Excel
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
          url: 'import_mc_excel.php',
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
  