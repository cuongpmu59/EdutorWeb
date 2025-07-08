// 📁 js/table.js

let table, currentRowIndex = null, currentRow = null;
const escapeHTML = str => (str || '').replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

function selectRow(row, data) {
  if (currentRow) currentRow.classList.remove("selected-row");
  currentRow = row;
  row.classList.add("selected-row");
  parent.postMessage({ ...data, type: "fillForm" }, "*");
  document.getElementById("previewArea").innerHTML = `
    <strong>Câu hỏi:</strong><br>${escapeHTML(data.question)}<br><br>
    ${data.answer1 !== undefined ? `<strong>A:</strong> ${escapeHTML(data.answer1)}<br>` : ""}
    ${data.answer2 !== undefined ? `<strong>B:</strong> ${escapeHTML(data.answer2)}<br>` : ""}
    ${data.answer3 !== undefined ? `<strong>C:</strong> ${escapeHTML(data.answer3)}<br>` : ""}
    ${data.answer4 !== undefined ? `<strong>D:</strong> ${escapeHTML(data.answer4)}<br>` : ""}
    <br><strong>Đúng:</strong> <span style="color:green;font-weight:bold;">${escapeHTML(data.correct_answer)}</span><br>
    <strong>Chủ đề:</strong> ${escapeHTML(data.topic)}<br>
    ${data.image ? `<img src="${escapeHTML(data.image)}" style="max-height:120px;margin-top:10px;border:1px solid #ccc;">` : ""}
  `;
  MathJax.typesetPromise?.();
}

function showImage(src) {
  document.getElementById("modalImage").src = src;
  document.getElementById("imageModal").style.display = "flex";
}
function closeModal() {
  document.getElementById("imageModal").style.display = "none";
}

function rowKeyNavigation(e) {
  const rowCount = table.rows({ search: "applied" }).count();
  if (rowCount === 0) return;
  if (currentRowIndex === null) currentRowIndex = 0;
  else if (e.key === "ArrowDown" && currentRowIndex < rowCount - 1) currentRowIndex++;
  else if (e.key === "ArrowUp" && currentRowIndex > 0) currentRowIndex--;

  const rowNode = table.row(currentRowIndex, { search: "applied" }).node();
  if (!rowNode) return;
  const tds = $(rowNode).find("td");
  const data = {
    id: tds.eq(0).text().trim(), question: tds.eq(1).text().trim(),
    answer1: tds.eq(2)?.text().trim(), answer2: tds.eq(3)?.text().trim(),
    answer3: tds.eq(4)?.text().trim(), answer4: tds.eq(5)?.text().trim(),
    correct_answer: tds.eq(6)?.text().trim(), topic: tds.eq(7)?.text().trim(),
    image: tds.eq(8).find('img').attr('src') || ""
  };
  selectRow(rowNode, data);
  rowNode.scrollIntoView({ behavior: "smooth", block: "center" });
}

window.addEventListener("keydown", e => {
  if (e.key === "Escape") closeModal();
  if (e.key === "ArrowDown" || e.key === "ArrowUp") {
    e.preventDefault(); rowKeyNavigation(e);
  }
});

$(document).ready(() => {
  table = $('#questionTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
      { extend: 'excelHtml5', text: '📥 Xuất Excel', title: 'Danh sách câu hỏi' },
      { extend: 'print', text: '🖨️ In bảng', title: 'Danh sách câu hỏi' }
    ],
    pageLength: 20,
    lengthMenu: [ [10, 20, 50, 100, -1], [10, 20, 50, 100, "Tất cả"] ],
    language: {
      search: "🔍 Tìm kiếm:", lengthMenu: "Hiển thị _MENU_ dòng",
      info: "Hiển thị _START_ đến _END_ trong _TOTAL_ dòng",
      zeroRecords: "Không tìm thấy kết quả phù hợp", infoEmpty: "Không có dữ liệu",
      paginate: { first: "«", last: "»", next: "›", previous: "‹" }
    },
    order: [[0, 'desc']]
  });

  $('#questionTable tbody').on('click', 'tr', function () {
    const tds = $(this).find("td");
    const data = {
      id: tds.eq(0).text().trim(), question: tds.eq(1).text().trim(),
      answer1: tds.eq(2)?.text().trim(), answer2: tds.eq(3)?.text().trim(),
      answer3: tds.eq(4)?.text().trim(), answer4: tds.eq(5)?.text().trim(),
      correct_answer: tds.eq(6)?.text().trim(), topic: tds.eq(7)?.text().trim(),
      image: tds.eq(8).find('img').attr('src') || ""
    };
    currentRowIndex = table.row(this, { search: 'applied' }).index();
    selectRow(this, data);
  });

  $("#filterTopicInline").on("change", function () {
    const topic = this.value;
    const url = new URL(window.location.href);
    url.searchParams.set("topic", topic);
    window.location.href = url.toString();
  });

  $("#excelInput").on("change", function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (evt) {
      const workbook = XLSX.read(evt.target.result, { type: "binary" });
      const sheet = workbook.Sheets[workbook.SheetNames[0]];
      const rawRows = XLSX.utils.sheet_to_json(sheet, { header: 1 });

      const rows = rawRows.filter(row => row.length >= 6 && row[0] !== "ID");
      const formatted = rows.map(r => ({
        question: r[1] || '', answer1: r[2] || '', answer2: r[3] || '', answer3: r[4] || '',
        answer4: r[5] || '', correct_answer: r[6] || '', topic: r[7] || '', image: r[8] || ''
      }));

      $.post(location.pathname, { excelData: JSON.stringify(formatted) })
        .done(res => {
          if (res.trim() === "OK") {
            alert("✅ Nhập dữ liệu thành công!");
            location.reload();
          } else alert("Lỗi khi lưu dữ liệu:\n" + res);
        })
        .fail(err => alert("Không kết nối được đến máy chủ."));
    };
    reader.readAsBinaryString(file);
  });

  $(".tab-button").click(function () {
    $(".tab-button").removeClass("active");
    $(this).addClass("active");
    const tabId = $(this).data("tab");
    $(".tab-content").removeClass("active");
    $("#" + tabId).addClass("active");
  });
});
