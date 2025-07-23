document.addEventListener("keydown", function (e) {
  const table = $('#mcTable').DataTable();
  const rows = table.rows({ page: 'current' }).nodes();
  const $rows = $(rows);
  const current = $rows.filter('.selected');
  let index = $rows.index(current);

  if (e.key === "ArrowDown") {
    e.preventDefault();
    if (index < $rows.length - 1) {
      $rows.removeClass("selected");
      const nextRow = $rows.eq(index + 1);
      nextRow.addClass("selected");
      nextRow[0].scrollIntoView({ block: "nearest", behavior: "smooth" });
      sendRowData(nextRow);
    }
  }

  if (e.key === "ArrowUp") {
    e.preventDefault();
    if (index > 0) {
      $rows.removeClass("selected");
      const prevRow = $rows.eq(index - 1);
      prevRow.addClass("selected");
      prevRow[0].scrollIntoView({ block: "nearest", behavior: "smooth" });
      sendRowData(prevRow);
    }
  }
});

// Hàm gửi dữ liệu về form cha qua postMessage
function sendRowData(row) {
  const cells = row.find("td");
  const data = {
    type: "mc_select_row",
    mc_id: cells.eq(0).data("raw"),
    mc_topic: cells.eq(1).data("raw"),
    mc_question: cells.eq(2).data("raw"),
    mc_answer1: cells.eq(3).data("raw"),
    mc_answer2: cells.eq(4).data("raw"),
    mc_answer3: cells.eq(5).data("raw"),
    mc_answer4: cells.eq(6).data("raw"),
    mc_correct_answer: cells.eq(7).data("raw"),
    mc_image_url: cells.eq(8).data("raw")
  };

  window.parent.postMessage(data, "*");
}
