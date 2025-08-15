// Yêu cầu: đã include thư viện SheetJS (xlsx.full.min.js) trong HTML

document.getElementById('btnImportExcel')?.addEventListener('click', () => {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.xlsx, .xls';

    input.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        try {
            const data = await file.arrayBuffer();
            const workbook = XLSX.read(data, { type: 'array' });
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            const jsonData = XLSX.utils.sheet_to_json(firstSheet, { defval: "" });

            if (jsonData.length === 0) {
                alert("File Excel không có dữ liệu.");
                return;
            }

            // Gửi dữ liệu JSON lên server
            const response = await fetch('../../includes/mc/mc_table_import_excel.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();

            if (result.status === 'success') {
                alert(`Đã nhập thành công ${result.inserted} dòng.\n${result.errors.length > 0 ? "Có lỗi: " + result.errors.join("\n") : ""}`);
                if (typeof table !== 'undefined') {
                    table.ajax.reload(null, false); // reload DataTable
                }
            } else {
                alert("Lỗi nhập dữ liệu: " + result.message);
            }

        } catch (err) {
            console.error(err);
            alert("Không thể đọc file Excel.");
        }
    });

    input.click();
});
