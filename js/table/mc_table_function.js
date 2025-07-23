$(document).ready(function () {
  const table = $('#mcTable').DataTable({
    ajax: {
      url: '../../api/mc/get_all.php',
      dataSrc: ''
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
      {
        data: 'mc_image_url',
        render: function (data) {
          if (!data) return '';
          return `<img src="${data}" class="thumb" onerror="this.style.display='none'">`;
        }
      }
    ],
    createdRow: function (row, data) {
      const fields = [
        'mc_id', 'mc_topic', 'mc_question',
        'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4',
        'mc_correct_answer', 'mc_image_url'
      ];
      $('td', row).each(function (index) {
        $(this).attr('data-raw', data[fields[index]]);
      });
    },
    responsive: true,
    scrollX: true,
    initComplete: function (settings, json) {
      const topics = [...new Set(json.map(q => q.mc_topic).filter(Boolean))].sort();
      const $filter = $('#topicFilter');
      if ($filter.length > 0) {
        topics.forEach(topic => {
          $filter.append(`<option value="${topic}">${topic}</option>`);
        });

        $filter.on('change', function () {
          const selected = $(this).val();
          if (selected) {
            table.column(1).search('^' + selected + '$', true, false).draw();
          } else {
            table.column(1).search('').draw();
          }
        });
      }
    }
  });
});
