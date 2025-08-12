document.addEventListener("DOMContentLoaded", function () {
    // Lưu cache tạm ở client (chỉ trong phiên hiện tại)
    let tableCache = {};

    let table = $('#mcTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            let cacheKey = JSON.stringify(data);

            // Nếu đã có trong cache => trả về ngay
            if (tableCache[cacheKey]) {
                callback(tableCache[cacheKey]);
                return;
            }

            $.ajax({
                url: "../../pages/mc/mc_table_data.php", // PHP server-side
                type: "GET", // Hoặc POST nếu bạn sửa PHP nhận POST
                data: data,
                dataType: "json",
                success: function (json) {
                    // Lưu vào cache
                    tableCache[cacheKey] = json;
                    callback(json);
                },
                error: function (xhr, error, thrown) {
                    console.error("DataTables AJAX Error:", xhr.responseText);
                    alert("Lỗi tải dữ liệu bảng!\n" + thrown);
                }
            });
        },
        columns: [
            { data: "id" },
            { data: "mc_topic" },
            { data: "mc_question" },
            { data: "mc_a" },
            { data: "mc_b" },
            { data: "mc_c" },
            { data: "mc_d" },
            { data: "mc_correct" }
        ],
        order: [[0, "desc"]],
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        language: {
            processing: "Đang tải dữ liệu...",
            search: "Tìm kiếm:",
            lengthMenu: "Hiển thị _MENU_ dòng",
            info: "Hiển thị _START_ đến _END_ của _TOTAL_ dòng",
            infoEmpty: "Không có dữ liệu",
            zeroRecords: "Không tìm thấy dữ liệu phù hợp",
            paginate: {
                first: "Đầu",
                last: "Cuối",
                next: "Sau",
                previous: "Trước"
            }
        }
    });

    // Xóa cache khi tìm kiếm để lấy dữ liệu mới
    $('#mcTable_filter input').on('input', function () {
        tableCache = {};
    });

    // Xóa cache khi đổi số lượng hiển thị
    $('#mcTable_length select').on('change', function () {
        tableCache = {};
    });
});
