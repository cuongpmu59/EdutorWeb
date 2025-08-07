$(document).ready(function () {
  const table = $('#mcTable').DataTable({
    ajax: '/../../includes/mc/mc_fetch_data.php',
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
          return `<img src="${thumbUrl}" alt="Ảnh" style="border-radius:4px;object-fit:cover;" width="50" height="50" loading="lazy">`;
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
    drawCallback: function () {
      if (window.MathJax?.typesetPromise) {
        MathJax.typesetPromise().catch((err) => console.error('MathJax error:', err));
      }
    }
  });

  // Khi click vào 1 dòng → gửi dữ liệu lên form cha qua postMessage
  $('#mcTable tbody').on('click', 'tr', function () {
    const rowData = table.row(this).data();
    if (!rowData || Object.keys(rowData).length === 0 || !rowData.mc_id) return;

    $('#mcTable tbody tr').removeClass('selected');
    $(this).addClass('selected');

    $.ajax({
      url: '/../../includes/mc/mc_fetch_data.php',
      method: 'POST',
      data: { mc_id: rowData.mc_id },
      dataType: 'json',
      success: function (response) {
        if (response && !response.error && window.parent) {
          window.parent.postMessage({ type: 'fill-form', data: response }, '*');
        } else {
          alert(typeof response.error === 'string' ? response.error : '❌ Không thể tải dữ liệu chi tiết.');
        }
      },
      error: function (xhr, status, error) {
        alert('❌ Lỗi AJAX: ' + error);
      }
    });
  });
});
