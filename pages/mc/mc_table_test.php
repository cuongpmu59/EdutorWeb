<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Test Import Excel</title>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
<h3>📂 Chọn file Excel để test import</h3>
<input type="file" id="importExcelInput" accept=".xlsx">
<pre id="result"></pre>

<script>
$('#importExcelInput').on('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(evt) {
        const data = new Uint8Array(evt.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheetName = workbook.SheetNames[0];
        const worksheet = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], { defval: '' });

        console.log("Worksheet:", worksheet);

        $.ajax({
            url: 'mc_table_import_excel.php', // chỉnh đường dẫn
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ rows: worksheet }),
            dataType: 'json',
            success: function(res) {
                console.log(res);
                $('#result').text(JSON.stringify(res, null, 2));
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                $('#result').text("Lỗi gửi dữ liệu tới server");
            }
        });
    };
    reader.readAsArrayBuffer(file);
});
</script>
</body>
</html>
