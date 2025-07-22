$(document).ready(function () {
    $('#excelFile').on('change', function (e) {
      const file = e.target.files[0];
      if (!file) return;
  
      const reader = new FileReader();
  
      reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const worksheet = workbook.Sheets[workbook.SheetNames[0]];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: '' });
  
        // Kiểm tra các cột bắt buộc
        const requiredFields = [
          'mc_topic',
          'mc_question',
          'mc_answer1',
          'mc_answer2',
          'mc_answer3',
          'mc_answer4',
          'mc_correct_answer'
        ];
  
        const missingFields = requiredFields.filter(f => !(f in jsonData[0]));
  
        if (missingFields.length > 0) {
          alert("❌ Thiếu các cột bắt buộc: " + missingFields.join(', '));
          return;
        }
  
        // Gửi dữ liệu lên server
        $.ajax({
          url: '../../includes/mc_import_excel.php',
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(jsonData),
          success: function (res) {
            alert("✅ Đã nhập " + res.inserted + " câu hỏi!");
            location.reload();
          },
          error: function () {
            alert("❌ Lỗi khi nhập file Excel.");
          }
        });
      };
  
      reader.readAsArrayBuffer(file);
    });
  });
  