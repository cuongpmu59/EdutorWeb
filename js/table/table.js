$(document).ready(function () {
  // === 1. Khởi tạo bảng DataTable ===
  const table = $('#mcTable').DataTable({
    dom: 'Bfrtip',
    buttons: ['excel', 'print'],
    pageLength: 10,
    language: {
      search: "🔍 Tìm kiếm:",
      lengthMenu: "Hiển thị _MENU_ mục",
      info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
      infoEmpty: "Không có dữ liệu",
      zeroRecords: "Không tìm thấy kết quả phù hợp",
      paginate: {
        first: "Đầu",
        last: "Cuối",
        next: "▶",
        previous: "◀"
      },
    },
    initComplete: function () {
      $('.buttons-excel, .buttons-print').hide(); // Ẩn nút export mặc định
    }
  });

  // === 2. Bộ lọc chủ đề ===
  $('#filterTopic').on('change', function () {
    const topic = $(this).val();
    const url = new URL(window.location.href);

    if (topic) {
      url.searchParams.set('topic', topic);
    } else {
      url.searchParams.delete('topic');
    }

    window.location.href = url.toString(); // Tải lại với filter mới
  });

  // === 3. Chuyển tab giao diện ===
  $('.tab-button').on('click', function () {
    const tabId = $(this).data('tab');
    $('.tab-button').removeClass('active');
    $(this).addClass('active');
    $('.tab-content').removeClass('active');
    $('#' + tabId).addClass('active');
  });

  // === 4. Xem ảnh lớn khi click ảnh ===
  $('.thumb').on('click', function () {
    const src = $(this).attr('src');
    if (src) window.open(src, '_blank');
  });

  // === 5. Click chọn dòng để gửi dữ liệu ===
  $('#mcTable tbody').on('click', 'tr', function () {
    $('#mcTable tbody tr').removeClass('selected');
    $(this).addClass('selected');
    const rowData = table.row(this).data();
    sendRowDataToParent(rowData);
  });

  // === 6. Di chuyển bằng bàn phím ↑ / ↓ ===
  $(document).on('keydown', function (e) {
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      const current = $('#mcTable tbody tr.selected');
      let next;

      if (e.key === 'ArrowDown') {
        next = current.length ? current.next() : $('#mcTable tbody tr').first();
      } else {
        next = current.length ? current.prev() : $('#mcTable tbody tr').last();
      }

      if (next.length) {
        current.removeClass('selected');
        next.addClass('selected');
        next[0].scrollIntoView({ behavior: 'smooth', block: 'center' });

        const rowData = table.row(next).data();
        sendRowDataToParent(rowData);
      }
    }
  });

  // === 7. Gửi dữ liệu dòng được chọn về parent ===
  function sendRowDataToParent(rowData) {
    if (!rowData || window.parent === window) return;

    // Tách ảnh từ cột 8
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
