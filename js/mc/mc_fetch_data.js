$(document).ready(function () {
  $('#mcTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
          url: '../../includes/mc/mc_table_data.php',
          type: 'GET'
      },
      columns: [
          { data: 'mc_id' },
          { data: 'mc_topic' },
          { data: 'mc_question' },
          { data: 'mc_answer1' },
          { data: 'mc_answer2' },
          { data: 'mc_answer3' },
          { data: 'mc_answer4' },
          { data: 'mc_correct_answer' },
          { data: 'mc_image_url', render: function (data) {
              return data ? `<img src="${data}" width="50">` : '';
          }}
      ]
  });
});
