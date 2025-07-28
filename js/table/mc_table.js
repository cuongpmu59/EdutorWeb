import { initExcelExport } from './mc_table_excel.js';

$(document).ready(function () {
  const table = initDataTable();       // Gọi hàm khởi tạo bảng
  initExcelExport(table);              
  // các init khác (image, truyền dữ liệu, arrow...) nếu có
});

// ✅ Hàm khởi tạo DataTable + YADCF lọc theo "Chủ đề"
export function initDataTable() {
  const table = $('#mcTable').DataTable({
    dom: 'Bfrtip', 
    paging: true,
    searching: true,
    responsive: true,
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
    }
  });

  // ✅ Lọc bằng YADCF (ở cột 1 là "Chủ đề")
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
