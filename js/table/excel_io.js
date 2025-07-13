// === 📁 Nhập Excel ===
$('#btnImportExcel').on('click', function () {
    $('#excelInput').click();
  });
  
  $('#excelInput').on('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
  
    const reader = new FileReader();
    reader.onload = function (e) {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const sheet = workbook.Sheets[workbook.SheetNames[0]];
      const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });
  
      console.log("📥 Dữ liệu Excel:", jsonData);
      alert("✅ Đã đọc file Excel!\nDữ liệu hiển thị trong console.");
    };
    reader.readAsArrayBuffer(file);
  });
  
  // === ⬇️ Xuất Excel ===
  $('#btnExportExcel').on('click', function () {
    $('.buttons-excel').click();
  });
  
  // === 🖨️ In bảng ===
  $('#btnPrintTable').on('click', function () {
    $('.buttons-print').click();
  });
  