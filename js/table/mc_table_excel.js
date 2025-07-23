document.getElementById('excelFile').addEventListener('change', function (e) {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();

  reader.onload = function (event) {
    try {
      const data = new Uint8Array(event.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const sheetName = workbook.SheetNames[0];
      const worksheet = workbook.Sheets[sheetName];
      const json = XLSX.utils.sheet_to_json(worksheet, { defval: '' });

      if (!json.length) {
        alert('❌ File Excel không có dữ liệu.');
        return;
      }

      // Map dữ liệu theo cấu trúc của bảng
      const formatted = json.map(row => ({
        mc_id: row.ID || '',
        mc_topic: row.ChuDe || '',
        mc_question: row.CauHoi || '',
        mc_answer1: row.A || '',
        mc_answer2: row.B || '',
        mc_answer3: row.C || '',
        mc_answer4: row.D || '',
        mc_correct_answer: row.DapAn || '',
        mc_image_url: row.Anh || ''
      }));

      const table = $('#mcTable').DataTable();
      table.clear().rows.add(formatted).draw();
      MathJax.typeset(); // Render lại MathJax nếu có công thức

      alert(`✅ Đã nhập ${formatted.length} dòng từ Excel.`);
    } catch (err) {
      console.error(err);
      alert('❌ Lỗi khi đọc file Excel. Đảm bảo đúng định dạng.');
    }
  };

  reader.readAsArrayBuffer(file);
});
