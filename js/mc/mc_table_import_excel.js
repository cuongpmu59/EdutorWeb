// js/mc/mc_table_import_excel.js

$(document).ready(function () {
    const $input = $("#importExcelInput");
  
    if (!$input.length) return; // không có input thì thoát
  
    $input.on("change", function (e) {
      const file = e.target.files[0];
      if (!file) return;
  
      const reader = new FileReader();
  
      reader.onload = function (evt) {
        try {
          const data = new Uint8Array(evt.target.result);
          const workbook = XLSX.read(data, { type: "array" });
  
          // lấy sheet đầu tiên
          const sheetName = workbook.SheetNames[0];
          const worksheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], {
            defval: "",
          });
  
          if (!worksheet.length) {
            toastr.warning("📂 File Excel rỗng!");
            resetInput();
            return;
          }
  
          toastr.info("⏳ Đang nhập dữ liệu, vui lòng chờ...");
  
          // gửi dữ liệu lên server
          $.ajax({
            url: "../../includes/mc/mc_table_import_excel.php",
            type: "POST",
            data: { rows: JSON.stringify(worksheet) },
            dataType: "json",
            success: function (res) {
              if (res.status === "success") {
                toastr.success(`📥 Nhập thành công ${res.count} dòng!`);
                if (typeof table !== "undefined") {
                  table.ajax.reload(null, false);
                }
              } else {
                toastr.error(res.message || "❌ Lỗi khi nhập Excel");
              }
            },
            error: function () {
              toastr.error("❌ Không thể gửi dữ liệu lên server");
            },
            complete: function () {
              resetInput();
            },
          });
        } catch (err) {
          console.error(err);
          toastr.error("❌ Không thể đọc file Excel");
          resetInput();
        }
      };
  
      reader.readAsArrayBuffer(file);
    });
  
    function resetInput() {
      $input.val("");
    }
  });
  