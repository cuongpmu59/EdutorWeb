$(document).ready(function () {
  const table = $('#tfTable').DataTable({
    ajax: '../../includes/tf/tf_fetch_data.php',
    columns: [
      { data: 'tf_id', title: 'ID' },
      { data: 'tf_topic', title: 'Chủ đề' },
      { data: 'tf_question', title: 'Câu hỏi' },
      { data: 'tf_statement1', title: '1' },
      { data: 'tf_statement2', title: '2' },
      { data: 'tf_statement3', title: '3' },
      { data: 'tf_statement4', title: '4' },
      { 
        data: null, 
        title: 'Đúng/Sai', 
        render: function(data) {
          let answers = [];
          for (let i = 1; i <= 4; i++) {
            if (data['tf_correct_answer'+i] !== undefined && data['tf_correct_answer'+i] !== null) {
              answers.push(`${i}: ${data['tf_correct_answer'+i] == 1 ? 'Đúng' : 'Sai'}`);
            }
          }
          return answers.join('; ');
        }
      },
      {
        data: 'tf_image_url',
        title: 'Ảnh',
        render: function(data) {
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

  // Click vào dòng → gửi dữ liệu chi tiết về form cha
  $('#tfTable tbody').on('click', 'tr', function () {
    const rowData = table.row(this).data();
    if (!rowData || !rowData.tf_id) return;

    $('#tfTable tbody tr').removeClass('selected');
    $(this).addClass('selected');

    $.ajax({
      url: '../../includes/tf/tf_fetch_data.php',
      method: 'POST',
      data: { tf_id: rowData.tf_id },
      dataType: 'json',
      success: function(response) {
        if (response && !response.error && window.parent) {
          window.parent.postMessage({ type: 'fill-form', data: response }, '*');
        } else {
          alert(response.error || '❌ Không thể tải dữ liệu chi tiết.');
        }
      },
      error: function(xhr, status, error) {
        alert('❌ Lỗi AJAX: ' + error);
      }
    });
  });
});
