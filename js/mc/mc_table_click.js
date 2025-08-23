$(document).ready(function () {
    const table = $('#mcTable').DataTable();

    // Khi click vào 1 dòng trong bảng
    $('#mcTable tbody').on('click', 'tr', function () {
        const rowData = table.row(this).data();
        if (!rowData) return;

        // Gán dữ liệu vào form
        $('#mc_topic').val(rowData.mc_topic);
        $('#mc_question').val(rowData.mc_question);
        $('#mc_answer1').val(rowData.mc_answer1);
        $('#mc_answer2').val(rowData.mc_answer2);
        $('#mc_answer3').val(rowData.mc_answer3);
        $('#mc_answer4').val(rowData.mc_answer4);
        $('#mc_correct_answer').val(rowData.mc_correct_answer);

        // Hình ảnh
        if (rowData.mc_image_url) {
            $('#mc_image_url').val(rowData.mc_image_url);
            $('#mc_preview_image').attr('src', rowData.mc_image_url).show();
        } else {
            $('#mc_image_url').val('');
            $('#mc_preview_image').attr('src', '').hide();
        }
    });
});

