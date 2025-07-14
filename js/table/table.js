$(document).ready(function () {
  // 🔤 Accent-neutralize cho tìm kiếm tiếng Việt
  $.fn.dataTable.ext.type.search.string = function (data) {
    return !data
      ? ''
      : data
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .toLowerCase();
  };

  // 🎯 Khởi tạo DataTable
  const table = $('#mcTable').DataTable({
    dom: 'Bfrtip',
    buttons: ['excelHtml5', 'print'],
    fixedHeader: true,
    scrollX: true,
    drawCallback: function () {
      if (window.MathJax) MathJax.typeset();
    },
    language: {
      search: "🔍 Tìm kiếm:",
      lengthMenu: "Hiển thị _MENU_ dòng",
      info: "Trang _PAGE_ / _PAGES_ (_TOTAL_ dòng)",
      infoEmpty: "Không có dữ liệu",
      zeroRecords: "Không tìm thấy kết quả phù hợp",
      paginate: {
        first: "«", last: "»", next: "▶", previous: "◀"
      }
    }
  });

  // 📤 Nút xuất Excel/In bảng
  $('.buttons-excel, .buttons-print').hide();
  $('#btnExportExcel').on('click', () => $('.buttons-excel').click());
  $('#btnPrintTable').on('click', () => $('.buttons-print').click());

  // 🖼️ Click ảnh thu nhỏ → mở lớn
  $('#mcTable').on('click', 'img.thumb', function () {
    const src = $(this).attr('src');
    if (src) window.open(src, '_blank');
  });

  // 🧠 Click chọn dòng → gửi dữ liệu về form cha
  $('#mcTable tbody').on('click', 'tr', function () {
    $('#mcTable tbody tr').removeClass('selected');
    $(this).addClass('selected');
    const rowData = table.row(this).data();
    sendRowDataToParent(rowData);
  });

  // ⌨️ Điều hướng bằng phím ↑ ↓
  $(document).on('keydown', function (e) {
    const selected = $('#mcTable tbody tr.selected');
    let next;
    if (e.key === 'ArrowDown') {
      next = selected.length ? selected.next() : $('#mcTable tbody tr').first();
    } else if (e.key === 'ArrowUp') {
      next = selected.length ? selected.prev() : $('#mcTable tbody tr').last();
    }
    if (next?.length) {
      selected.removeClass('selected');
      next.addClass('selected');
      next[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
      const rowData = table.row(next).data();
      sendRowDataToParent(rowData);
    }
  });

  // 📤 Gửi dòng đã chọn về form cha qua postMessage
  function sendRowDataToParent(rowData) {
    if (!rowData || window.parent === window) return;
    const imgSrc = $('<div>').html(rowData[8]).find('img').attr('src') || '';
    window.parent.postMessage({
      type: 'mc_selected_row',
      data: {
        mc_id: rowData[0],
        mc_topic: rowData[1],
        mc_question: rowData[2],
        mc_answer1: rowData[3],
        mc_answer2: rowData[4],
        mc_answer3: rowData[5],
        mc_answer4: rowData[6],
        mc_correct_answer: rowData[7],
        mc_image_url: imgSrc
      }
    }, '*');
  }

  // 📚 Lọc chủ đề (chuyển trang để filter qua server)
  $('#topicSelect').on('change', function () {
    const topic = $(this).val();
    const url = topic ? `mc_table.php?topic=${encodeURIComponent(topic)}` : 'mc_table.php';
    window.location.href = url;
  });

  // 📥 Nhận lệnh cuộn đến tab từ form cha
  window.addEventListener('message', function (event) {
    if (event.data?.type === 'scrollToListTab') {
      document.querySelector('.tab-button[data-tab="listTab"]')?.click();
      document.getElementById('listTab')?.scrollIntoView({ behavior: 'smooth' });
    }
  });
});
