$(document).ready(function () {
  $('#mcTable tbody').on('click', 'tr', function () {
    const mc_id = $(this).find('td:first').data('raw'); // Cột đầu tiên chứa mc_id

    if (!mc_id) return;

    $.ajax({
      url: '../includes/mc_fetch.php',
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
