$(document).ready(function () {
  $('#mcTable tbody').on('click', 'tr', function () {
    const mc_id = $(this).children('td').eq(0).data('raw');
    
    if (mc_id) {
      window.parent.postMessage({
        type: 'mc_select_row',
        data: { mc_id }
      }, '*');
    }
  });
});
