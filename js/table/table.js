$(document).ready(function () {
  // === 1. Khởi tạo DataTable ===
  const table = $('#mcTable').DataTable({
    dom: 'Bfrtip',
    buttons: ['excelHtml5', 'print'],
    pageLength: 10,
    lengthMenu: [5, 10, 25, 50, 100],
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
      $('.buttons-excel, .buttons-print').hide(); // Ẩn nút export mặc định nếu cần
    }
  });

  // === 2. Bộ lọc theo chủ đề ===
  $('#filterTopic').on('change', function () {
    const topic = $(this).val();
    const url = new URL(window.location.href);
    topic ? url.searchParams.set('topic', topic) : url.searchParams.delete('topic');
    window.location.href = url.toString();
  });

  // === 3. Tab giao diện ===
  $('.tab-button').on('click', function () {
    const tabId = $(this).data('tab');
    $('.tab-button').removeClass('active');
    $(this).addClass('active');
    $('.tab-content').removeClass('active');
    $('#' + tabId).addClass('active');
  });

  // === 4. Xem ảnh lớn khi click ảnh ===
  $('#mcTable').on('click', 'img.thumb', function () {
    const src = $(this).attr('src');
    if (src) window.open(src, '_blank');
  });

  // === 5. Click dòng → chọn + gửi dữ liệu ===
  $('#mcTable tbody').on('click', 'tr', function () {
    $('#mcTable tbody tr').removeClass('selected');
    $(this).addClass('selected');
    const rowData = table.row(this).data();
    sendRowDataToParent(rowData);
  });

  // === 6. Di chuyển bằng phím ↑ / ↓ ===
  $(document).on('keydown', function (e) {
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      const current = $('#mcTable tbody tr.selected');
      let nextRow;

      if (e.key === 'ArrowDown') {
        nextRow = current.length ? current.next() : $('#mcTable tbody tr').first();
      } else {
        nextRow = current.length ? current.prev() : $('#mcTable tbody tr').last();
      }

      if (nextRow.length) {
        current.removeClass('selected');
        nextRow.addClass('selected');
        nextRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });

        const rowData = table.row(nextRow).data();
        sendRowDataToParent(rowData);
      }
    }
  });

  // === 7. Gửi dữ liệu về form cha ===
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
});
