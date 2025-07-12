document.addEventListener("DOMContentLoaded", () => {
  const table = $('#mcTable').DataTable({
    pageLength: 20,
    language: {
      search: "🔍 Tìm kiếm:",
      lengthMenu: "Hiển thị _MENU_ dòng",
      info: "Hiển thị _START_ đến _END_ trong _TOTAL_ dòng",
      zeroRecords: "Không có dữ liệu",
      infoEmpty: "Không có dữ liệu",
      paginate: {
        first: "«", last: "»", next: "›", previous: "‹"
      }
    },
    order: [[0, 'desc']]
  });

  // Bộ lọc theo chủ đề
  const topicSelect = document.getElementById("filterTopic");
  if (topicSelect) {
    topicSelect.addEventListener("change", () => {
      const topic = topicSelect.value;
      location.href = topic ? `mc_table.php?topic=${encodeURIComponent(topic)}` : 'mc_table.php';
    });
  }

  // Hiển thị modal ảnh nếu cần
  const modal = document.createElement("div");
  modal.id = "imageModal";
  modal.style.cssText = `
    display:none; position:fixed; z-index:9999; top:0; left:0;
    width:100%; height:100%; background:rgba(0,0,0,0.85);
    justify-content:center; align-items:center;
  `;
  modal.innerHTML = `<span style="position:absolute;top:10px;right:20px;font-size:28px;color:#fff;cursor:pointer;">&times;</span>
    <img id="modalImage" style="max-width:90%;max-height:90%;">`;
  document.body.appendChild(modal);

  modal.querySelector("span").onclick = () => (modal.style.display = "none");

  document.querySelectorAll("img.thumb").forEach(img => {
    img.addEventListener("click", () => {
      const modalImg = document.getElementById("modalImage");
      modalImg.src = img.src;
      modal.style.display = "flex";
    });
  });

  // Nhập Excel (nếu có input)
  const excelInput = document.getElementById("excelInput");
  if (excelInput) {
    excelInput.addEventListener("change", function () {
      const file = this.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (e) {
        const workbook = XLSX.read(e.target.result, { type: "binary" });
        const sheet = workbook.Sheets[workbook.SheetNames[0]];
        const rawRows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        const rows = rawRows.filter(row => row.length >= 8 && row[0] !== "ID");
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

        fetch("mc_table.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "excelData=" + encodeURIComponent(JSON.stringify(formatted))
        })
        .then(res => res.text())
        .then(res => {
          if (res.trim() === "OK") {
            alert("✅ Nhập dữ liệu thành công!");
            location.reload();
          } else {
            alert("Lỗi khi lưu dữ liệu:\n" + res);
          }
        })
        .catch(err => alert("Không kết nối được đến máy chủ."));
      };
      reader.readAsBinaryString(file);
    });
  }
});
