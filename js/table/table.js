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
      // Đảm bảo các nút Excel / Print ẩn khỏi giao diện nếu không cần
      $('.buttons-excel').hide();
      $('.buttons-print').hide();
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

    // Tải lại trang với chủ đề đã lọc
    window.location.href = url.toString();
  });

  // === 3. Chuyển tab giao diện ===
  $('.tab-button').on('click', function () {
    const tabId = $(this).data('tab');
    $('.tab-button').removeClass('active');
    $(this).addClass('active');
    $('.tab-content').removeClass('active');
    $('#' + tabId).addClass('active');
  });

  // === 4. Xem ảnh lớn (nếu muốn mở modal sau này) ===
  $('.thumb').on('click', function () {
    const src = $(this).attr('src');
    if (!src) return;
    window.open(src, '_blank');
  });

  // === 5. Điều hướng bằng phím ↑ và ↓ (nếu dùng iframe) ===
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

        // Gửi dữ liệu dòng được chọn cho form cha (qua postMessage nếu có)
        const rowData = table.row(next).data();
        if (window.parent !== window) {
          window.parent.postMessage({ type: 'mc_selected_row', data: rowData }, '*');
        }
      }
    }
  });

  // === 6. Click chọn dòng để gửi dữ liệu về form cha ===
  $('#mcTable tbody').on('click', 'tr', function () {
    $('#mcTable tbody tr').removeClass('selected');
    $(this).addClass('selected');

    const rowData = table.row(this).data();
    if (window.parent !== window) {
      window.parent.postMessage({ type: 'mc_selected_row', data: rowData }, '*');
    }
  });
});
