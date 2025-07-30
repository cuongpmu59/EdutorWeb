// js/table/mc_get_table.js
$(document).ready(function () {
    initMcQuestionTable();
  });
  
  function initMcQuestionTable() {
    $('#mcTable').DataTable({
      ajax: '../../includes/mc_get_data.php',
      columns: [
        { data: 'mc_id', title: 'ID' },
        { data: 'mc_topic', title: 'Chủ đề' },
        { data: 'mc_question', title: 'Câu hỏi' },
        { data: 'mc_answer1', title: 'Đáp án 1' },
        { data: 'mc_answer2', title: 'Đáp án 2' },
        { data: 'mc_answer3', title: 'Đáp án 3' },
        { data: 'mc_answer4', title: 'Đáp án 4' },
        { data: 'mc_correct_anwer', title: 'Đúng' },
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
      pageLength: 10
    });
  }
  