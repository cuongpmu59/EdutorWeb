// js/table/mc_table_excel.js

export function initExcelExport(dataTableInstance) {
  new $.fn.dataTable.Buttons(dataTableInstance, {
      buttons: [
          {
              extend: 'excelHtml5',
              title: 'Danh_sach_cau_hoi',
              text: '📥 Xuất Excel',
              className: 'mc-export-excel',
              exportOptions: {
                  columns: ':visible'
              }
          }
      ]
  });

  dataTableInstance.buttons(0, null).container().appendTo('#mcTable_wrapper .mc-export-container');
}
