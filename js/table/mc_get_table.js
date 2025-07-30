// Khởi động bảng dataTable
$(document).ready(function () {
  initMcQuestionTable();
});

function initMcQuestionTable() {
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

  // ⬇️ Gửi dữ liệu khi click dòng
  $('#mcTable tbody').on('click', 'tr', function () {
    const data = table.row(this).data();
    if (!data) return;
    window.parent.postMessage({ type: 'fill-form', data }, '*');
  });
}
