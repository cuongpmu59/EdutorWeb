function sendRowData(row) {
    const data = row.data();
    if (!data) return;
  
    const $row = row.node();
    const cells = $($row).find('td');
  
    const message = {
      type: "mc_select_row",
      data: {
        id: cells.eq(0).data('raw'),
        topic: cells.eq(1).data('raw'),
        question: cells.eq(2).data('raw'),
        answer1: cells.eq(3).data('raw'),
        answer2: cells.eq(4).data('raw'),
        answer3: cells.eq(5).data('raw'),
        answer4: cells.eq(6).data('raw'),
        correct: cells.eq(7).data('raw'),
        image: cells.eq(8).find('img').length > 0 ? cells.eq(8).find('img').attr('src') : ''
      }
    };
  
    window.parent.postMessage(message, "*");
  }
  
  $(document).ready(function () {
    $('#mcTable tbody').on('click', 'tr', function () {
      mcTable.$('tr.selected').removeClass('selected');
      $(this).addClass('selected')[0].scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });
      sendRowData(mcTable.row(this));
    });
  });
  