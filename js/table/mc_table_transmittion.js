$(document).ready(function () {
  $('#mcTable').on('click', '.btn-edit', function () {
    const mc_id = $(this).data('id');

    $.ajax({
      url: '../../includes/mc_fetch.php',
      method: 'GET',
      data: { mc_id },
      dataType: 'json',
      success: function (data) {
        if (data.error) {
          alert('❌ ' + data.error);
          return;
        }

        // ✅ Gửi dữ liệu lên form bằng postMessage
        window.parent.postMessage({ type: 'fill-form', data }, '*');
      },
      error: function () {
        alert('❌ Lỗi khi lấy dữ liệu câu hỏi.');
      }
    });
  });
});
