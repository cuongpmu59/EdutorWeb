$(document).ready(function () {
  // Khởi tạo DataTable
  const table = $('#mcTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
      { extend: 'excelHtml5', text: '📥 Xuất Excel', title: 'Danh sách câu hỏi' },
      { extend: 'print', text: '🖨️ In bảng', title: 'Danh sách câu hỏi' }
    ],
    pageLength: 20,
    lengthMenu: [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Tất cả"]],
    language: {
      search: "🔍 Tìm kiếm:",
      lengthMenu: "Hiển thị _MENU_ dòng",
      info: "Hiển thị _START_ đến _END_ trong _TOTAL_ dòng",
      zeroRecords: "Không có dữ liệu",
      infoEmpty: "Không có dữ liệu",
      paginate: { first: "«", last: "»", next: "›", previous: "‹" }
    },
    order: [[0, 'desc']]
  });

  // Lọc theo chủ đề
  $('#filterTopic').on('change', function () {
    const topic = this.value;
    const url = topic ? `mc_table.php?topic=${encodeURIComponent(topic)}` : 'mc_table.php';
    location.href = url;
  });

  // Tabs
  $(".tab-button").click(function () {
    $(".tab-button").removeClass("active");
    $(this).addClass("active");
    const tabId = $(this).data("tab");
    $(".tab-content").removeClass("active");
    $("#" + tabId).addClass("active");
  });

  // Nhập từ Excel
  $("#excelInput").on("change", function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (evt) {
      const workbook = XLSX.read(evt.target.result, { type: "binary" });
      const sheet = workbook.Sheets[workbook.SheetNames[0]];
      const rawRows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

      const rows = rawRows.filter(r => r.length >= 8 && r[0] !== "ID");
      const formatted = rows.map(r => ({
        mc_question: r[2] || '',
        mc_answer1: r[3] || '',
        mc_answer2: r[4] || '',
        mc_answer3: r[5] || '',
        mc_answer4: r[6] || '',
        mc_correct_answer: r[7] || '',
        mc_topic: r[1] || '',
        mc_image_url: r[8] || ''
      }));

      $.post("mc_table.php", { excelData: JSON.stringify(formatted) })
        .done(res => {
          if (res.trim() === "OK") {
            alert("✅ Nhập dữ liệu thành công!");
            location.reload();
          } else {
            alert("❌ Lỗi khi lưu dữ liệu:\n" + res);
          }
        })
        .fail(() => {
          alert("❌ Không kết nối được đến máy chủ.");
        });
    };
    reader.readAsBinaryString(file);
  });
});
