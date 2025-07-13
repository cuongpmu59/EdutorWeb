$(document).ready(function () {
  // Click nút "Nhập Excel" → mở chọn file
  $('#btnImportExcel').on('click', function () {
    $('#excelInput').click();
  });

  // Khi chọn file Excel
  $('#excelInput').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: 'array' });

      // Lấy sheet đầu tiên
      const sheetName = workbook.SheetNames[0];
      const worksheet = workbook.Sheets[sheetName];
      const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

      console.log("📥 Dữ liệu Excel:", jsonData);

      // Optional: preview, validate, import to DB
      alert(`✅ Đã đọc ${jsonData.length - 1} dòng dữ liệu từ Excel.`);
    };
    reader.readAsArrayBuffer(file);
  });

  // Click "Xuất Excel"
  $('#btnExportExcel').on('click', function () {
    $('.buttons-excel').click();
  });

  // Click "In bảng"
  $('#btnPrintTable').on('click', function () {
    $('.buttons-print').click();
  });
});
