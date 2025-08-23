$(document).ready(function () {
    const table = $('#mcTable').DataTable();

    $('#mcTable tbody').on('click', 'tr', function () {
        // Bỏ highlight cũ + thêm highlight dòng chọn
        $('#mcTable tbody tr').removeClass('selected');
        $(this).addClass('selected');

        // Lấy dữ liệu từ DataTable
        const rowData = table.row(this).data();
        if (!rowData) return;

        // Giả sử rowData theo đúng thứ tự cột: 
        // 0=mc_id, 1=mc_topic, 2=mc_question, 3=mc_answer1, 4=mc_answer2,
        // 5=mc_answer3, 6=mc_answer4, 7=mc_correct_answer, 8=mc_image_url

        $('#mc_topic').val(rowData[1]);
        $('#mc_question').val(rowData[2]);
        $('#mc_answer1').val(rowData[3]);
        $('#mc_answer2').val(rowData[4]);
        $('#mc_answer3').val(rowData[5]);
        $('#mc_answer4').val(rowData[6]);
        $('#mc_correct_answer').val(rowData[7]);

        // Xử lý ảnh nếu có
        if (rowData[8]) {
            $('#mc_image_url').val(rowData[8]); // set vào input ẩn/hiện URL
            $('#mc_preview_image').attr('src', rowData[8]).show(); // hiển thị ảnh preview
        } else {
            $('#mc_image_url').val('');
            $('#mc_preview_image').attr('src', '').hide();
        }
    });
});
