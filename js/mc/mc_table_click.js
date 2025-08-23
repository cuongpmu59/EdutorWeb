// mc_table_click.js
$(document).ready(function () {
    const table = $('#mcTable').DataTable();

    // Click vào 1 dòng
    $('#mcTable tbody').on('click', 'tr', function () {
        const rowData = table.row(this).data();
        if (!rowData) return;

        // Highlight dòng chọn
        $('#mcTable tbody tr').removeClass('selected');
        $(this).addClass('selected');

        // Gán dữ liệu vào form
        $('#mc_topic').val(rowData.mc_topic || '');
        $('#mc_question').val(rowData.mc_question || '');
        $('#mc_answer1').val(rowData.mc_answer1 || '');
        $('#mc_answer2').val(rowData.mc_answer2 || '');
        $('#mc_answer3').val(rowData.mc_answer3 || '');
        $('#mc_answer4').val(rowData.mc_answer4 || '');
        $('#mc_correct_answer').val(rowData.mc_correct_answer || '');

        // Hình ảnh (nếu có)
        if (rowData.mc_image_url) {
            $('#mc_image_url').val(rowData.mc_image_url);
            $('#mc_preview_image').attr('src', rowData.mc_image_url).show();
        } else {
            $('#mc_image_url').val('');
            $('#mc_preview_image').attr('src', '').hide();
        }

        // Scroll tới form & focus vào ô đầu tiên
        $('html, body').animate({
            scrollTop: $('#mcForm').offset().top - 20
        }, 400);
        $('#mc_topic').focus();
    });
});
