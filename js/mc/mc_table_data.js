document.addEventListener("DOMContentLoaded", function () {
    let tableCache = {};

    let table = $('#mcTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: function (data, callback) {
            let cacheKey = JSON.stringify(data);

            // Nếu dữ liệu đã được cache
            if (tableCache[cacheKey]) {
                callback(tableCache[cacheKey]);
                return;
            }

            $.ajax({
                url: "../../pages/mc/mc_table_data.php",
                type: "POST", // Dùng POST để an toàn
                data: data,
                dataType: "json",
                success: function (json) {
                    if (!json || typeof json !== "object" || !json.data) {
                        console.error("Invalid JSON format:", json);
                        alert("Lỗi dữ liệu từ server!");
                        return;
                    }
                    tableCache[cacheKey] = json;
                    callback(json);
                },
                error: function (xhr, status, thrown) {
                    console.error("AJAX Error:", status, thrown, xhr.responseText);
                    alert("Không thể tải dữ liệu!\n" + xhr.responseText);
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

    // Xóa cache khi tìm kiếm hoặc thay đổi số bản ghi
    $('#mcTable_filter input, #mcTable_length select').on('input change', function () {
        tableCache = {};
    });
});
