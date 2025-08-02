$(document).ready(function () {
  // Khởi tạo DataTable
  const table = $('#mcTable').DataTable({
    ajax: '../../includes/mc/mc_fetch_data.php',
    columns: [
      { data: 'mc_id', title: 'ID' },
      { data: 'mc_topic', title: 'Chủ đề' },
      { data: 'mc_question', title: 'Câu hỏi' },
      { data: 'mc_answer1', title: 'A' },
      { data: 'mc_answer2', title: 'B' },
      { data: 'mc_answer3', title: 'C' },
      { data: 'mc_answer4', title: 'D' },
      { data: 'mc_correct_answer', title: 'Đáp án' },
      {
        data: 'mc_image_url',
        title: 'Ảnh',
        render: function (data) {
          if (!data) return '';
          const thumbUrl = data.includes('/upload/')
            ? data.replace('/upload/', '/upload/w_50,h_50,c_fill/')
            : data;
          return `<img src="${thumbUrl}" alt="Ảnh" width="50" height="50">`;
        },
        orderable: false,
        searchable: false
      }
    ],
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
    },
    responsive: true,
    pageLength: 10,

    // Vẽ lại MathJax khi trang thay đổi
    drawCallback: function () {
      if (window.MathJax) {
        MathJax.typesetPromise();
      }
    }
  });

  // Xử lý khi người dùng click vào một dòng trong bảng
  $('#mcTable tbody').on('click', 'tr', function () {
    const rowData = table.row(this).data();
    if (!rowData || !rowData.mc_id) return;

    // Làm nổi bật dòng được chọn
    $('#mcTable tbody tr').removeClass('selected');
    $(this).addClass('selected');

    // Gửi AJAX để lấy chi tiết dòng được chọn
    $.ajax({
      url: '../../includes/mc/mc_fetch_data.php',
      method: 'POST',
      data: { mc_id: rowData.mc_id },
      dataType: 'json',
      success: function (response) {
        if (response && !response.error && window.parent) {
          // Gửi dữ liệu lên form cha
          window.parent.postMessage({ type: 'fill-form', data: response }, '*');
        } else {
          alert(response.error || '❌ Không thể tải dữ liệu chi tiết.');
        }
      },
      error: function (xhr, status, error) {
        alert('❌ Lỗi AJAX: ' + error);
      }
    });
  });

  // Nghe các sự kiện gửi từ form cha
  window.addEventListener('message', function (event) {
    const { type, data } = event.data || {};

    // Xử lý yêu cầu xoá một dòng
    if (type === 'delete-row' && data && data.mc_id) {
      const mc_id = data.mc_id;

      if (!confirm('❓ Bạn có chắc muốn xoá dòng này?')) return;

      $.ajax({
        url: '../../includes/mc/mc_fetch_data.php',
        method: 'POST',
        dataType: 'json',
        data: { delete_mc_id: mc_id },
        success: function (response) {
          if (response.success) {
            alert('✅ Đã xoá thành công');
            table.ajax.reload(null, false); // Reload giữ nguyên trang hiện tại
          } else {
            alert(response.error || '❌ Xoá thất bại');
          }
        },
        error: function () {
          alert('❌ Lỗi khi gửi yêu cầu xoá');
        }
      });
    }

    // Reload bảng nếu có yêu cầu
    if (event.data?.action === 'reload_table') {
      table.ajax.reload(null, false);
    }
  });
});
