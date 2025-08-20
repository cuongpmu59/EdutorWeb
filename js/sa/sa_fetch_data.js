$(document).ready(function () {
    const table = $('#saTable').DataTable({
      ajax: '../../includes/sa/sa_fetch_data.php',
      columns: [
        { data: 'sa_id', title: 'ID' },
        { data: 'sa_topic', title: 'Chủ đề' },
        { data: 'sa_question', title: 'Câu hỏi' },
        { data: 'sa_correct_answer', title: 'Đáp án' },
        {
          data: 'sa_image_url',
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
  
    // Khi click vào 1 dòng → gửi dữ liệu lên form cha qua postMessage
    $('#saTable tbody').on('click', 'tr', function () {
      const rowData = table.row(this).data();
      if (!rowData || !rowData.sa_id) return;
  
      $('#saTable tbody tr').removeClass('selected');
      $(this).addClass('selected');
  
      $.ajax({
        url: '../../includes/sa/sa_fetch_data.php',
        method: 'POST',
        data: { sa_id: rowData.sa_id },
        dataType: 'json',
        success: function (response) {
          if (response && !response.error && window.parent) {
            window.parent.postMessage({ type: 'fill-form', data: response }, '*');
          } else {
            alert(response.error || '❌ Không thể tải dữ liệu chi tiết.');
          }
        },
        error: function (xhr, status, error) {
          alert('❌ Lỗi AJAX: ' + error);
        }
      });
    });
  });
  