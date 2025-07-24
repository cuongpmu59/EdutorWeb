$(document).ready(function () {
  // Bắt sự kiện khi người dùng click vào một dòng trong bảng
  $('#mcTable tbody').on('click', 'tr', function () {
    const table = $('#mcTable').DataTable();
    const rowData = table.row(this).data();

    if (!rowData) return;

    // 👉 Làm nổi dòng đang chọn
    $('#mcTable tbody tr').removeClass('selected');
    $(this).addClass('selected');

    // Gửi dữ liệu về form cha qua postMessage
    const dataToSend = {
      type: 'mc_select_row',
      payload: {
        mc_id: rowData[0],               // ID
        mc_topic: rowData[1],            // Chủ đề
        mc_question: rowData[2],         // Câu hỏi
        mc_answer1: rowData[3],          // Đáp án A
        mc_answer2: rowData[4],          // Đáp án B
        mc_answer3: rowData[5],          // Đáp án C
        mc_answer4: rowData[6],          // Đáp án D
        mc_correct_answer: rowData[7],   // Đáp án đúng
        mc_image_url: extractImageSrc(rowData[8]) // Ảnh (nếu có)
      }
    };

    window.parent.postMessage(dataToSend, '*');

    // (Tuỳ chọn) Cuộn lên đầu trang cha để dễ nhìn form
    window.parent.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // Hàm phụ: lấy src từ thẻ <img> nếu có
  function extractImageSrc(html) {
    if (!html) return '';
    const match = html.match(/<img.*?src=["'](.*?)["']/i);
    return match ? match[1] : '';
  }
});
