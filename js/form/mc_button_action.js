document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("mcForm");
    const btnSave = document.getElementById("btnSave");
    const btnDelete = document.getElementById("btnDelete");
    const btnReset = document.getElementById("btnReset");
    const mcIdInput = document.getElementById("mc_id");
    const imageInput = document.getElementById("mc_image");

    const CLOUDINARY_URL = "https://api.cloudinary.com/v1_1/YOUR_CLOUD_NAME/image/upload";
    const CLOUDINARY_UPLOAD_PRESET = "YOUR_UNSIGNED_PRESET";

    btnSave.addEventListener("click", async function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        // Nếu có ảnh và là câu hỏi cập nhật -> đổi tên ảnh
        const isUpdating = mcIdInput.value.trim() !== "";

        if (imageInput.files.length > 0 && isUpdating) {
            const renamedFile = await renameFileForCloudinary(imageInput.files[0], `mc_${mcIdInput.value}`);
            const cloudUrl = await uploadToCloudinary(renamedFile, `mc_${mcIdInput.value}`);
            formData.set("existing_image", cloudUrl);
            formData.delete("image"); // không gửi file gốc
        }

        fetch("../../includes/mc_save.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.text())
            .then(async (responseText) => {
                // Nếu là thêm mới và có ảnh, giờ mới biết mc_id trả về
                const newId = extractInsertedId(responseText); // tùy vào cách PHP phản hồi
                if (!isUpdating && imageInput.files.length > 0 && newId) {
                    const renamedFile = await renameFileForCloudinary(imageInput.files[0], `mc_${newId}`);
                    const cloudUrl = await uploadToCloudinary(renamedFile, `mc_${newId}`);

                    // Gửi lại yêu cầu cập nhật ảnh (chỉ update mc_image_url)
                    const updateData = new FormData();
                    updateData.append("mc_id", newId);
                    updateData.append("topic", "");
                    updateData.append("question", "");
                    updateData.append("answer1", "");
                    updateData.append("answer2", "");
                    updateData.append("answer3", "");
                    updateData.append("answer4", "");
                    updateData.append("answer", "");
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
            mcIdInput.value = "";
        }
    });

    function renameFileForCloudinary(file, publicId) {
        return new File([file], file.name, { type: file.type });
    }

    async function uploadToCloudinary(file, publicId) {
        const data = new FormData();
        data.append("file", file);
        data.append("upload_preset", CLOUDINARY_UPLOAD_PRESET);
        data.append("public_id", publicId);

        const res = await fetch(CLOUDINARY_URL, {
            method: "POST",
            body: data
        });
        const json = await res.json();
        return json.secure_url;
    }

    function extractInsertedId(responseText) {
        // Tuỳ vào PHP, ví dụ nếu PHP trả JSON: { "mc_id": 123 }
        try {
            const json = JSON.parse(responseText);
            return json.mc_id || null;
        } catch (e) {
            return null;
        }
    }
});
