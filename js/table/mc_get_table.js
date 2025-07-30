$(document).ready(function () {
    $('#mcTable').DataTable({
      ajax: '../../includes/mc_get_data.php',
      columns: [
        { data: 'mc_id' },
        { data: 'mc_topic' },
        { data: 'mc_question' },
        { data: 'mc_answer1' },
        { data: 'mc_answer2' },
        { data: 'mc_answer3' },
        { data: 'mc_answer4' },
        { data: 'mc_correct_anwer' },
        {
          data: 'mc_image_url',
          render: function (data, type, row) {
            if (!data) return '';
            return `<img src="${data}" alt="ảnh" style="width:50px; height:auto;">`;
          },
          orderable: false,
          searchable: false
        }
      ]
    });
  });
  