document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("mcForm");
  const imageInput = document.getElementById("mc_image");
  const imagePreview = document.getElementById("mc_imagePreview");
  const iframe = document.getElementById("mcIframe");
  const toggleIframeBtn = document.getElementById("toggleIframeBtn");

  // Toggle iframe bảng câu hỏi
  toggleIframeBtn.addEventListener("click", () => {
    const isShown = iframe.style.display !== "none";
    iframe.style.display = isShown ? "none" : "block";
    toggleIframeBtn.textContent = isShown ? "🔼 Hiện bảng câu hỏi" : "🔽 Ẩn bảng câu hỏi";
  });

  // Load ảnh
  document.getElementById("loadImageBtn").addEventListener("click", () => imageInput.click());
  imageInput.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        imagePreview.src = e.target.result;
        imagePreview.style.display = "block";
      };
      reader.readAsDataURL(file);
    }
  });

  // Xoá ảnh
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
        document.getElementById("saveBtn").click();
      } else {
        alert("❌ Lỗi khi xoá ảnh.");
      }
    } catch (err) {
      alert("❌ Xảy ra lỗi khi xoá ảnh.");
    }
  });

  // Gửi form
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);

    try {
      const res = await fetch("/utils/mc_save.php", {
        method: "POST",
        body: formData
      });
      const text = await res.text();
      let result;
      try {
        result = JSON.parse(text);
      } catch {
        alert("❌ Phản hồi không hợp lệ:\n" + text);
        return;
      }

      if (result.success) {
        alert("✅ " + result.message);
        form.reset();
        imagePreview.style.display = "none";
        iframe.style.display = "block";
        iframe.src = iframe.src;
        if (result.id) window.postMessage({ type: "mc_saved", id: result.id }, "*");
        updateFullPreview(); // cập nhật lại preview tổng
      } else {
        alert("❌ " + (result.message || "Lỗi không xác định"));
      }
    } catch (err) {
      alert("❌ Lỗi kết nối: " + err.message);
    }
  });

  // Nhận dữ liệu từ iframe
  window.addEventListener("message", (event) => {
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

      updateFullPreview(); // cập nhật lại toàn bộ preview
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  });

  // Toggle xem trước toàn bộ
  const togglePreviewFull = document.getElementById("togglePreviewFull");
  const previewFullBox = document.getElementById("previewFullBox");
  const previewFullContent = document.getElementById("previewFullContent");

  togglePreviewFull.addEventListener("click", () => {
    const isHidden = previewFullBox.style.display === "none";
    previewFullBox.style.display = isHidden ? "block" : "none";
    if (isHidden) updateFullPreview();
  });

  function updateFullPreview() {
    const q = document.getElementById("mc_question").value;
    const a1 = document.getElementById("mc_answer1").value;
    const a2 = document.getElementById("mc_answer2").value;
    const a3 = document.getElementById("mc_answer3").value;
    const a4 = document.getElementById("mc_answer4").value;
    const correct = document.getElementById("mc_correct_answer").value;
    const imgSrc = imagePreview?.src || "";

    let html = `<p><strong>Câu hỏi:</strong><br>${q}</p>`;
    if (imgSrc && imagePreview.style.display !== "none") {
      html += `<p><img src="${imgSrc}" style="max-width:100%; height:auto;"></p>`;
    }

    html += `<p><strong>Đáp án:</strong><ul>`;
    [["A", a1], ["B", a2], ["C", a3], ["D", a4]].forEach(([label, val]) => {
      const highlight = label === correct ? '✅' : '';
      html += `<li><strong>${label}:</strong> ${val} ${highlight}</li>`;
    });
    html += `</ul></p>`;

    previewFullContent.innerHTML = html;
    if (window.MathJax) MathJax.typesetPromise([previewFullContent]);
  }

  // Cập nhật preview khi gõ
  ["mc_question", "mc_answer1", "mc_answer2", "mc_answer3", "mc_answer4", "mc_correct_answer"].forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener("input", () => {
      if (previewFullBox.style.display !== "none") updateFullPreview();
    });
  });
});
