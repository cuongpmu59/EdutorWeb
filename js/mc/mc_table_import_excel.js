// mc_table_import_excel.js
// Chức năng: Đọc file Excel (.xlsx), gửi dữ liệu JSON tới server để import DB

function importExcel(inputSelector, table) {
    const $input = $(inputSelector);
  
    $input.on('change', function (e) {
      const file = e.target.files[0];
      if (!file) return;
  
      // Đọc file bằng FileReader
      const reader = new FileReader();
      reader.onload = function (evt) {
        try {
          const data = new Uint8Array(evt.target.result);
          const workbook = XLSX.read(data, { type: 'array' });
  
          // Lấy sheet đầu tiên
          const sheetName = workbook.SheetNames[0];
          const worksheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], { defval: '' });
  
          if (!worksheet.length) {
            toastr.warning("📂 File Excel rỗng!");
            $input.val('');
            return;
          }
  
          toastr.info("⏳ Đang nhập dữ liệu, vui lòng chờ...");
  
          // Gửi dữ liệu sang PHP
          $.ajax({
            url: "../../includes/mc/mc_table_import_excel.php",
            method: "POST",
            data: { rows: JSON.stringify(worksheet) },
            dataType: "json",
            success: function (res) {
              if (res.status === "success") {
                toastr.success(`📥 Nhập thành công ${res.count} dòng!`);
                if (table) table.ajax.reload();
              } else {
                toastr.error(res.message || "❌ Lỗi khi nhập Excel");
              }
            },
            error: function () {
              toastr.error("❌ Lỗi khi gửi dữ liệu tới server");
            },
            complete: function () {
              $input.val(""); // reset input
            }
          });
  
        } catch (err) {
          console.error(err);
          toastr.error("❌ Không thể đọc file Excel");
          $input.val("");
        }
      };
  
      reader.readAsArrayBuffer(file);
    });
  }
  
  // Khi DOM ready thì gắn event
  $(function () {
    const table = $('#mcTable').DataTable();
    importExcel('#importExcelInput', table);
  });
  