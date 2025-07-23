// Hàm đọc Excel và cập nhật bảng
document.getElementById('excelFile').addEventListener('change', function (e) {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function (evt) {
    const data = evt.target.result;
    const workbook = XLSX.read(data, { type: 'binary' });
    const firstSheet = workbook.SheetNames[0];
    const sheetData = XLSX.utils.sheet_to_json(workbook.Sheets[firstSheet], { defval: '' });

    if (sheetData.length === 0) {
      alert('❌ File Excel không chứa dữ liệu.');
      return;
    }

    // Xóa dữ liệu cũ
    const table = $('#mcTable').DataTable();
    table.clear();

    // Duyệt từng dòng Excel và thêm vào bảng
    sheetData.forEach(row => {
      const imageHTML = row['Ảnh'] ? `<img src="${row['Ảnh']}" class="thumb" onerror="this.style.display='none'">` : '';
      table.row.add([
        row['ID'] || '',
        row['Chủ đề'] || '',
        row['Câu hỏi'] || '',
        row['A'] || '',
        row['B'] || '',
        row['C'] || '',
        row['D'] || '',
        row['Đáp án đúng'] || '',
        imageHTML
      ]);
    });

    table.draw();
    MathJax.typesetPromise(); // Re-render công thức
  };

  reader.readAsBinaryString(file);
});
