// import { initDataTable } from './mc_table_function.js';
import { initExcelExport } from './mc_table_excel.js';
// các import khác...

$(document).ready(function () {
    const table = initDataTable(); // Giả sử hàm này trả về instance của DataTable
    initExcelExport(table);
    // các init khác...
});

// Lọc theo chủ đề
export function initDataTable() {
    const table = $('#mcTable').DataTable({
      // các tùy chọn DataTables bạn đang dùng
      // ví dụ:
      paging: true,
      searching: true,
      responsive: true,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
      }
    });
  
    // ✅ Khởi tạo YADCF sau khi DataTable đã sẵn sàng
    yadcf.init(table, [
      {
        column_number: 1, // cột "Chủ đề"
        filter_type: 'select',
        filter_default_label: 'Tất cả chủ đề',
        style_class: 'form-select form-select-sm'
      }
    ]);
  
    return table;
  }
  

