// mc_table_click.js
$(document).ready(function () {
    const table = $('#mcTable').DataTable();

    $('#mcTable tbody').on('click', 'tr', function () {
        const rowData = table.row(this).data();
        if (!rowData) return;

        console.log('Row clicked:', rowData); // Debug

        // Highlight dòng chọn
        $('#mcTable tbody tr').removeClass('selected');
        $(this).addClass('selected');

        // Tự động gán dữ liệu cho form theo key
        for (const key in rowData) {
            if (!rowData.hasOwnProperty(key)) continue;

            const value = rowData[key] ?? '';
            const $field = $(`#${key}, [name="${key}"]`);

            if ($field.length) {
                $field.each(function () {
                    const type = $(this).attr('type');
                    const tag = this.tagName.toLowerCase();

                    if (tag === 'input' && type === 'radio') {
                        // Radio: chọn cái nào có value trùng
                        if ($(this).val() == value) {
                            $(this).prop('checked', true);
                        }
                    } else if (tag === 'input' && type === 'checkbox') {
                        // Checkbox: tick nếu value truthy
                        if (value === true || value === 1 || value === '1' || value === 'on') {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    } else if (tag === 'select' || tag === 'textarea' || tag === 'input') {
                        // Textbox, textarea, select
                        $(this).val(value);
                    }
                });
            }

            // Nếu là field ảnh → gán preview
            if (key === 'mc_image_url') {
                if (value) {
                    $('#mc_preview_image').attr('src', value).show();
                } else {
                    $('#mc_preview_image').attr('src', '').hide();
                }
            }
        }

        // Cuộn tới form và focus vào ô đầu tiên
        if ($('#mcForm').length) {
            $('html, body').animate({
                scrollTop: $('#mcForm').offset().top - 20
            }, 400);
            $('#mcForm').find('input, textarea, select').first().focus();
        }
    });
});
