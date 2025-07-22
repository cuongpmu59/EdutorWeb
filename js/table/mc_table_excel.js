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
  
        const requiredFields = ['mc_topic', 'mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer'];
        const missingFields = requiredFields.filter(f => !(f in jsonData[0]));
        if (missingFields.length > 0) {
          alert("❌ Thiếu các cột bắt buộc: " + missingFields.join(', '));
          return;
        }
  
        $.ajax({
          url: '../../includes/mc_import_excel.php',
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(jsonData),
          success: function (res) {
            alert("✅ Đã nhập " + res.inserted + " câu hỏi!");
  
            if (typeof mcTable !== 'undefined' && Array.isArray(res.data)) {
              res.data.forEach(row => {
                mcTable.row.add([
                  `<td data-raw="${row.mc_id}">${row.mc_id}</td>`,
                  `<td data-raw="${row.mc_topic}">${row.mc_topic}</td>`,
                  `<td data-raw="${row.mc_question}">${row.mc_question}</td>`,
                  `<td data-raw="${row.mc_answer1}">${row.mc_answer1}</td>`,
                  `<td data-raw="${row.mc_answer2}">${row.mc_answer2}</td>`,
                  `<td data-raw="${row.mc_answer3}">${row.mc_answer3}</td>`,
                  `<td data-raw="${row.mc_answer4}">${row.mc_answer4}</td>`,
                  `<td data-raw="${row.mc_correct_answer}">${row.mc_correct_answer}</td>`,
                  row.mc_image_url
                    ? `<td><img src="${row.mc_image_url}" class="thumb" onerror="this.style.display='none'"></td>`
                    : `<td></td>`
                ]).draw(false);
              });
            }
          },
          error: function () {
            alert("❌ Lỗi khi nhập file Excel.");
          }
        });
      };
  
      reader.readAsArrayBuffer(file);
    });
  });
  