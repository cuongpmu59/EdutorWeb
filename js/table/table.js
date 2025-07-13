$(document).ready(function () {
  const table = $('#mcTable').DataTable({
    dom: 'Bfrtip',
    buttons: ['excelHtml5', 'print'],
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50, 100],
    fixedHeader: true, // ⬅️ Dòng này là để cố định tiêu đề bảng
    language: {
      search: "🔍 Tìm kiếm:",
      lengthMenu: "Hiển thị _MENU_ dòng",
      info: "Trang _PAGE_ / _PAGES_ (_TOTAL_ dòng)",
      infoEmpty: "Không có dữ liệu",
      zeroRecords: "Không tìm thấy kết quả phù hợp",
      paginate: {
        first: "«",
        last: "»",
        next: "▶",
        previous: "◀"
      },
    },
    initComplete: function () {
      $('.buttons-excel, .buttons-print').hide(); // Ẩn export mặc định nếu không dùng
    }
  });

  // === Lọc chủ đề ===
  $('#filterTopic').on('change', function () {
    const topic = $(this).val();
    const url = new URL(window.location.href);
    topic ? url.searchParams.set('topic', topic) : url.searchParams.delete('topic');
    window.location.href = url.toString();
  });

  // === Tab chuyển giao diện (nếu dùng) ===
  $('.tab-button').on('click', function () {
    const tabId = $(this).data('tab');
    $('.tab-button').removeClass('active');
    $(this).addClass('active');
    $('.tab-content').removeClass('active');
    $('#' + tabId).addClass('active');
  });

  // === Click ảnh để xem lớn ===
  $('#mcTable').on('click', 'img.thumb', function () {
    const src = $(this).attr('src');
    if (src) window.open(src, '_blank');
  });

  // === Click dòng → gửi dữ liệu về form cha ===
  $('#mcTable tbody').on('click', 'tr', function () {
    $('#mcTable tbody tr').removeClass('selected');
    $(this).addClass('selected');
    const rowData = table.row(this).data();
    sendRowDataToParent(rowData);
  });

  // === Điều hướng bằng phím ↑ ↓ ===
  $(document).on('keydown', function (e) {
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      const current = $('#mcTable tbody tr.selected');
      let nextRow = e.key === 'ArrowDown'
        ? (current.length ? current.next() : $('#mcTable tbody tr').first())
        : (current.length ? current.prev() : $('#mcTable tbody tr').last());

      if (nextRow.length) {
        current.removeClass('selected');
        nextRow.addClass('selected');
        nextRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });

        const rowData = table.row(nextRow).data();
        sendRowDataToParent(rowData);
      }
    }
  });

  // === Gửi dòng được chọn về form cha ===
  function sendRowDataToParent(rowData) {
    if (!rowData || window.parent === window) return;

    const imageSrc = $('<div>').html(rowData[8]).find('img').attr('src') || '';
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
        mc_image_url: imageSrc
      }
    }, '*');
  }

  // === Nhận yêu cầu từ form cha (cuộn tab danh sách) ===
  window.addEventListener('message', function (event) {
    if (event.data?.type === 'scrollToListTab') {
      document.querySelector('.tab-button[data-tab="listTab"]')?.click();
      document.getElementById('listTab')?.scrollIntoView({ behavior: 'smooth' });
    }
  });
});
