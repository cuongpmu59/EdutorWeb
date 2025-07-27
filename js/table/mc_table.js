// import { initDataTable } from './mc_table_function.js'; // Đã tạm bỏ
import { initExcelExport } from './mc_table_excel.js';

$(document).ready(function () {
  const table = initDataTable();       // Gọi hàm khởi tạo bảng
  initExcelExport(table);              // Gắn nút xuất Excel
  // các init khác (image, truyền dữ liệu, arrow...) nếu có
});

// ✅ Hàm khởi tạo DataTable + YADCF lọc theo "Chủ đề"
export function initDataTable() {
  const table = $('#mcTable').DataTable({
    dom: 'Bfrtip', // thêm nút export ở trên bảng
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
      column_number: 1, // "Chủ đề"
      filter_type: 'select',
      filter_default_label: 'Tất cả chủ đề',
      style_class: 'form-select form-select-sm'
    }
  ]);

  return table;
}
