document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("mcForm");
    const btnSave = document.getElementById("btnSave");
    const btnDelete = document.getElementById("btnDelete");
    const btnReset = document.getElementById("btnReset");
    const mcIdInput = document.getElementById("mc_id");
    const imageInput = document.getElementById("mc_image");

    btnSave.addEventListener("click", async function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const isUpdating = mcIdInput && mcIdInput.value.trim() !== "";

        if (imageInput.files.length > 0) {
            const mcId = isUpdating ? mcIdInput.value.trim() : "temp_" + Date.now();
            const publicId = `mc_${mcId}`;
            try {
                const cloudUrl = await uploadSignedToCloudinary(imageInput.files[0], publicId);
                formData.set("existing_image", cloudUrl);
                formData.delete("image"); // không gửi file gốc
            } catch (err) {
                alert("Lỗi tải ảnh lên Cloudinary: " + err.message);
                return;
            }
        }

        fetch("../../includes/mc_save.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(async (responseText) => {
            const newId = extractInsertedId(responseText);
            if (!isUpdating && imageInput.files.length > 0 && newId) {
                const cloudUrl = await uploadSignedToCloudinary(imageInput.files[0], `mc_${newId}`);

                const updateData = new FormData();
                updateData.append("mc_id", newId);
                updateData.append("existing_image", cloudUrl);

                await fetch("../../includes/mc_save.php", {
                    method: "POST",
                    body: updateData
                });
            }

            alert("Đã lưu câu hỏi.");
            window.location.reload();
        })
        .catch(err => {
            console.error("Lỗi khi lưu:", err);
            alert("Lưu thất bại.");
        });
    });

    btnDelete.addEventListener("click", function () {
        if (confirm("Bạn có chắc chắn muốn xóa câu hỏi này?")) {
            const mcId = mcIdInput.value;
            if (!mcId) return;

            fetch("../../includes/mc_delete.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `mc_id=${mcId}`
            })
            .then(res => res.text())
            .then(() => {
                alert("Đã xóa.");
                window.location.reload();
            });
        }
    });

    btnReset.addEventListener("click", function () {
        if (confirm("Bạn có muốn làm mới toàn bộ form?")) {
            form.reset();
            if (mcIdInput) mcIdInput.value = "";
        }
    });

    function extractInsertedId(responseText) {
        try {
            const json = JSON.parse(responseText);
            return json.mc_id || null;
        } catch (e) {
            return null;
        }
    }

    async function uploadSignedToCloudinary(file, publicId) {
        const sigRes = await fetch("../../includes/cloudinary_signature.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ public_id: publicId })
        });

        if (!sigRes.ok) throw new Error("Không tạo được chữ ký");

        const sigData = await sigRes.json();
        const data = new FormData();
        data.append("file", file);
        data.append("api_key", sigData.api_key);
        data.append("timestamp", sigData.timestamp);
        data.append("public_id", sigData.public_id);
        data.append("signature", sigData.signature);

        const uploadRes = await fetch(`https://api.cloudinary.com/v1_1/${sigData.cloud_name}/image/upload`, {
            method: "POST",
            body: data
        });

        const uploadJson = await uploadRes.json();
        if (uploadJson.secure_url) return uploadJson.secure_url;
        else throw new Error("Upload thất bại");
    }
});
