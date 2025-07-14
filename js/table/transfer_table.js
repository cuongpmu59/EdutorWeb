$(document).ready(function () {
    const table = $('#mcTable').DataTable();
  
    $('#mcTable tbody').on('click', 'tr', function () {
      $('#mcTable tbody tr').removeClass('selected');
      $(this).addClass('selected');
      const rowData = table.row(this).data();
      sendRowDataToParent(rowData);
    });
  
    function sendRowDataToParent(rowData) {
      if (!rowData || window.parent === window) return;
      const imageSrc = $('<div>').html(rowData[8]).find('img').attr('src') || '';
      window.parent.postMessage({
        type: 'mc_selected_row',
        data: {
          mc_id: rowData[0],
          mc_topic: rowData[1],
          mc_question: rowData[2],
          mc_answer1: rowData[3],
          mc_answer2: rowData[4],
          mc_answer3: rowData[5],
          mc_answer4: rowData[6],
          mc_correct_answer: rowData[7],
          mc_image_url: imageSrc
        }
      }, '*');
    }
  
    // Nhận yêu cầu từ form cha: chuyển tab nếu cần
    window.addEventListener('message', function (event) {
        console.log("📥 Đã nhận từ iframe:", event.data);

      if (event.data?.type === 'scrollToListTab') {
        document.querySelector('.tab-button[data-tab="listTab"]')?.click();
        document.getElementById('listTab')?.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
  