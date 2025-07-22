$(document).ready(function () {
    $(document).on('keydown', function (e) {
      const selected = mcTable.row('.selected');
      if (!selected.node()) return;
  
      let index = selected.index();
      if (e.key === 'ArrowUp' && index > 0) index--;
      else if (e.key === 'ArrowDown' && index < mcTable.rows().count() - 1) index++;
      else return;
  
      e.preventDefault();
      mcTable.$('tr.selected').removeClass('selected');
      const nextRow = mcTable.row(index);
      $(nextRow.node()).addClass('selected')[0].scrollIntoView({
        behavior: 'smooth',
        block: 'center'
      });
      sendRowData(nextRow);
    });
  });
  