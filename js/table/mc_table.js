// import { initDataTable } from './mc_table_function.js';
import { initExcelExport } from './mc_table_excel.js';
// các import khác...

$(document).ready(function () {
    const table = initDataTable(); // Giả sử hàm này trả về instance của DataTable
    initExcelExport(table);
    // các init khác...
});
