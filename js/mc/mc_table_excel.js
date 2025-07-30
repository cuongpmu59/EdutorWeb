export function initDataTable() {
    const table = $('#mcTable').DataTable({
      dom: 'Bfrtip',
      buttons: [], // 🔥 THÊM DÒNG NÀY
      paging: true,
      searching: true,
      responsive: true,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
      }
    });
  
    yadcf.init(table, [
      {
        column_number: 1,
        filter_type: 'select',
        filter_container_id: 'yadcf-filter-container-topic',
        filter_default_label: 'Tất cả chủ đề',
        style_class: 'form-select form-select-sm'
      }
    ]);
  
    return table;
  }
  