// js/table/mc_get_table.js
$(document).ready(function () {
  const table = $('#mcTable').DataTable({
    ajax: '../../includes/mc_get_data.php',
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

    drawCallback: function () {
      if (window.MathJax) {
        MathJax.typesetPromise();
      }
    }
  });

  // ⬇️ Sự kiện click dòng
  $('#mcTable tbody').on('click', 'tr', function () {
    const row = table.row(this).data();
    if (!row || !row.mc_id) return;

    $.ajax({
      url: '../../includes/mc_get_data.php',
      method: 'POST',
      data: { mc_id: row.mc_id },
      dataType: 'json',
      success: function (response) {
        if (window.parent) {
          window.parent.postMessage({ type: 'fill-form', data: response }, '*');
        }
      },
      error: function (xhr, status, error) {
        alert('❌ Lỗi khi tải dữ liệu: ' + error);
      }
    });
  });
});
