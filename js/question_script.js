const $ = id => document.getElementById(id);

// Nhận dữ liệu từ iframe (get_question.php)
window.addEventListener("message", (event) => {
  const data = event.data;
  if (!data || typeof data !== "object") return;

  // Gán dữ liệu vào form
  $("question_id").value = data.id;
  $("topic").value = data.topic || "";
  $("question").value = data.question || "";
  $("answer1").value = data.answer1 || "";
  $("answer2").value = data.answer2 || "";
  $("answer3").value = data.answer3 || "";
  $("answer4").value = data.answer4 || "";
  $("correct_answer").value = data.correct_answer || "";
  $("image_url").value = data.image || "";

  // Hiển thị ảnh ở 2 tab
  if (data.image) {
    $("imageTabPreview").src = data.image;
    $("imageTabPreview").style.display = "block";
    $("imageTabFileName").textContent = "Đã có ảnh";
    $("delete_image_tab").style.display = "inline-block";

    $("preview_image").src = data.image;
    $("preview_image").style.display = "block";
  } else {
    $("imageTabPreview").style.display = "none";
    $("imageTabFileName").textContent = "";
    $("delete_image_tab").style.display = "none";
    $("preview_image").style.display = "none";
  }
  updatePreview();
});

// Xem trước công thức
["topic", "question", "answer1", "answer2", "answer3", "answer4"].forEach(id => {
  $(id).addEventListener("input", updatePreview);
});

function updatePreview() {
  const content = `
    <strong>Chủ đề:</strong> ${$("topic").value}<br>
    <strong>Câu hỏi:</strong><br> ${$("question").value}<br>
    <strong>Đáp án:</strong><br>
    A. ${$("answer1").value}<br>
    B. ${$("answer2").value}<br>
    C. ${$("answer3").value}<br>
    D. ${$("answer4").value}
  `;
  $("preview_area").innerHTML = content;
  MathJax.typesetPromise();
}

// Chọn ảnh từ tab "Ảnh minh hoạ"
$("select_image_tab").addEventListener("click", () => {
  $("image").click();
});

// Khi chọn ảnh -> Upload lên Cloudinary
$("image").addEventListener("change", async function () {
  const file = this.files[0];
  if (!file) return;

  const formData = new FormData();
  const tempName = "temp_" + Date.now();
  formData.append("file", file);
  formData.append("upload_preset", CLOUDINARY_UPLOAD_PRESET);
  formData.append("public_id", tempName);

  const res = await fetch(`https://api.cloudinary.com/v1_1/${CLOUDINARY_CLOUD_NAME}/image/upload`, {
    method: "POST",
    body: formData
  });

  const data = await res.json();
  if (data.secure_url) {
    $("image_url").value = data.secure_url;
    $("imageTabPreview").src = data.secure_url;
    $("imageTabPreview").style.display = "block";
    $("preview_image").src = data.secure_url;
    $("preview_image").style.display = "block";
    $("imageTabFileName").textContent = file.name;
    $("delete_image_tab").style.display = "inline-block";
  }
});

// Xoá ảnh
$("delete_image_tab").addEventListener("click", async () => {
  const url = $("image_url").value;
  if (!url) return;

  // Gửi xoá ảnh lên server
  await fetch("delete_cloudinary_image.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "url=" + encodeURIComponent(url)
  });

  // Xoá ảnh khỏi form
  $("image_url").value = "";
  $("imageTabPreview").style.display = "none";
  $("preview_image").style.display = "none";
  $("imageTabFileName").textContent = "";
  $("delete_image_tab").style.display = "none";
});

// Làm mới form
$("resetBtn").addEventListener("click", () => {
  $("questionForm").reset();
  $("question_id").value = "";
  $("image_url").value = "";
  $("imageTabPreview").style.display = "none";
  $("preview_image").style.display = "none";
  $("imageTabFileName").textContent = "";
  $("delete_image_tab").style.display = "none";
  updatePreview();
});

// Gửi form lưu hoặc cập nhật
$("questionForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const id = $("question_id").value;
  const url = id ? "update_question.php" : "insert_question.php";

  const formData = new FormData(this);

  // Nếu là thêm mới, đổi tên ảnh sau khi thêm xong
  if (!id && $("image_url").value.includes("temp_")) {
    formData.append("temp_image", $("image_url").value);
  }

  const res = await fetch(url, {
    method: "POST",
    body: formData
  });

  const result = await res.json();
  alert(result.message || "Đã lưu!");

  if (result.success) {
    if (result.new_id && $("image_url").value.includes("temp_")) {
      // Đổi tên ảnh thành pic_ID
      const renameRes = await fetch("rename_cloudinary_image.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `old_url=${encodeURIComponent($("image_url").value)}&new_name=pic_${result.new_id}`
      });

      const renameData = await renameRes.json();
      if (renameData.secure_url) {
        $("image_url").value = renameData.secure_url;
        $("imageTabPreview").src = renameData.secure_url;
        $("preview_image").src = renameData.secure_url;
      }
    }

    // Làm mới bảng
    document.getElementById("questionIframe").contentWindow.location.reload();
  }
});

// Xoá câu hỏi
$("deleteBtn").addEventListener("click", async () => {
  const id = $("question_id").value;
  if (!id || !confirm("Xác nhận xoá câu hỏi này?")) return;

  const res = await fetch("delete_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + id
  });

  const result = await res.json();
  alert(result.message || "Đã xoá!");

  if (result.success) {
    $("resetBtn").click();
    document.getElementById("questionIframe").contentWindow.location.reload();
  }
});
