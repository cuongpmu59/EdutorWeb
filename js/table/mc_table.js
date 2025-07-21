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
  
    $.get('includes/mc_filter.php', function (options) {
      $('#filter-topic').append(options);
    });
  
    $('#filter-topic').on('change', function () {
      table.column(1).search(this.value).draw();
    });
  
    $('#mcTable_filter input[type="search"]').on('keyup change', function () {
      table.search(this.value).draw();
    });
  
    $.fn.dataTable.ext.type.search.string = function (data) {
      return !data ? '' : data.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    };
  
    table.on('draw', function () {
      if (window.MathJax) MathJax.typesetPromise();
    });
  
    $(document).on('click', '.thumb', function () {
      $('#imgModalContent').attr('src', $(this).attr('src'));
      $('#imgModal').fadeIn();
    });
    $('#imgModal').on('click', function () {
      $(this).fadeOut();
    });
  
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
  
    $('#mcTable tbody').on('click', 'tr', function () {
      table.$('tr.selected').removeClass('selected');
      $(this).addClass('selected');
      sendRowData(table.row(this));
    });
  
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
  