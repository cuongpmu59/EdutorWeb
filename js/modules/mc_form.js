// js/modules/mc_form.js

const imageInput = document.getElementById("mc_image");
const imagePreview = document.getElementById("mc_imagePreview");
const saveBtn = document.getElementById("saveBtn");
const toggleIframeBtn = document.getElementById("toggleIframeBtn");

// ==== Load ảnh xem trước ====
document.getElementById("loadImageBtn").addEventListener("click", () => imageInput.click());

imageInput.addEventListener("change", function (e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = e => {
      imagePreview.src = e.target.result;
      imagePreview.style.display = "block";
    };
    reader.readAsDataURL(file);
  } else {
    imagePreview.style.display = "none";
  }
});

// ==== Gửi form lưu câu hỏi ====
document.getElementById("mcForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  saveBtn.disabled = true;

  const formData = new FormData(this);
  try {
    const res = await fetch("/utils/mc_save.php", {
      method: "POST",
      body: formData
    });
    const json = await res.json();

    if (json.success) {
      alert("✅ Đã lưu thành công!");
      document.getElementById("mcForm").reset();
      imagePreview.style.display = "none";

      const iframe = document.getElementById("mcIframe");
      iframe.style.display = "block";
      iframe.src = iframe.src;
    } else {
      alert("❌ " + (json.message || "Lỗi khi lưu."));
    }
  } catch (err) {
    alert("❌ Lỗi gửi yêu cầu: " + err.message);
  }

  saveBtn.disabled = false;
});

// ==== Nhận dữ liệu khi chọn dòng bảng ====
window.addEventListener("message", function (event) {
  const d = event.data;
  if (d.type === "mc_select_row") {
    const data = d.data;
    document.getElementById("mc_id").value = data.id || "";
    document.getElementById("mc_topic").value = data.topic || "";
    document.getElementById("mc_question").value = data.question || "";
    document.getElementById("mc_answer1").value = data.answer1 || "";
    document.getElementById("mc_answer2").value = data.answer2 || "";
    document.getElementById("mc_answer3").value = data.answer3 || "";
    document.getElementById("mc_answer4").value = data.answer4 || "";
    document.getElementById("mc_correct_answer").value = data.correct || "";

    if (data.image) {
      imagePreview.src = data.image;
      imagePreview.style.display = "block";
    } else {
      imagePreview.style.display = "none";
    }

    if (typeof updatePreviews === "function") {
      updatePreviews();
      if (window.MathJax) MathJax.typesetPromise();
    }

    window.scrollTo({ top: 0, behavior: "smooth" });
  }
});

// ==== Xoá ảnh minh hoạ ====
document.getElementById("deleteImageBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Câu hỏi chưa có ID. Không thể xoá ảnh.");
  if (!confirm("❌ Xác nhận xoá ảnh minh hoạ?")) return;

  try {
    const res = await fetch("/utils/mc_delete_image.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ mc_id: id })
    });
    const result = await res.json();
    if (result.success) {
      imagePreview.style.display = "none";
      imageInput.value = "";
      alert("🧹 Đã xoá ảnh!");
      saveBtn.click();
    } else {
      alert("❌ Lỗi khi xoá ảnh.");
    }
  } catch (err) {
    alert("❌ Xảy ra lỗi khi xoá ảnh.");
  }
});

// ==== Xoá câu hỏi ====
document.getElementById("deleteQuestionBtn").addEventListener("click", async () => {
  const id = document.getElementById("mc_id").value;
  if (!id) return alert("❗ Chưa có câu hỏi nào được chọn.");
  if (!confirm("🗑️ Bạn có chắc muốn xoá câu hỏi này?")) return;

  try {
    const res = await fetch("/utils/mc_delete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ mc_id: id })
    });
    const result = await res.json();
    if (result.success) {
      alert("🗑️ Đã xoá câu hỏi!");
      document.getElementById("mcForm").reset();
      imagePreview.style.display = "none";
      document.getElementById("mcIframe").src = document.getElementById("mcIframe").src;
    } else {
      alert("❌ Xoá thất bại.");
    }
  } catch (err) {
    alert("❌ Lỗi khi gửi yêu cầu xoá.");
  }
});

// ==== Toggle bảng câu hỏi ====
toggleIframeBtn.addEventListener("click", () => {
  const iframe = document.getElementById("mcIframe");
  const show = iframe.style.display === "none";
  iframe.style.display = show ? "block" : "none";
  toggleIframeBtn.textContent = show ? "🔽 Ẩn bảng câu hỏi" : "🔼 Hiện bảng câu hỏi";
});
