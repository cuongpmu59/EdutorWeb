document.addEventListener("DOMContentLoaded", function () {
  const btnViewList = document.getElementById("mc_view_list");
  const btnReset = document.getElementById("mc_reset");
  const btnTogglePreview = document.getElementById("mc_toggle_preview");

  const iframe = document.getElementById("mcTableFrame");
  const form = document.getElementById("mc_form");
  const imagePreview = document.getElementById("mc_image_preview");
  const fullPreview = document.getElementById("mc_preview_full");

  // === 1. Ẩn/Hiện iframe danh sách ===
  if (btnViewList && iframe) {
    btnViewList.addEventListener("click", function () {
      const isHidden =
        iframe.style.display === "none" ||
        getComputedStyle(iframe).display === "none";
      iframe.style.display = isHidden ? "block" : "none";
      this.textContent = isHidden ? "Ẩn danh sách" : "Hiện danh sách";
    });
  }

  // === 2. Đặt lại toàn bộ form ===
  if (btnReset && form) {
    btnReset.addEventListener("click", function () {
      form.reset();

      // Xóa ảnh minh hoạ
      if (imagePreview) {
        imagePreview.src = "";
        imagePreview.style.display = "none";
      }

      // Xóa xem trước toàn bộ
      if (fullPreview) {
        fullPreview.innerHTML = "";
        fullPreview.style.display = "none";
      }

      // Focus lại vào trường đầu tiên
      const firstInput = form.querySelector("input, textarea, select");
      if (firstInput) firstInput.focus();
    });
  }

  // === 3. Ẩn/Hiện xem trước toàn bộ ===
  if (btnTogglePreview && fullPreview) {
    btnTogglePreview.addEventListener("click", function () {
      const isHidden =
        fullPreview.style.display === "none" ||
        getComputedStyle(fullPreview).display === "none";
      fullPreview.style.display = isHidden ? "block" : "none";
      this.textContent = isHidden ? "Ẩn xem trước" : "Xem trước toàn bộ";

      // Kích hoạt MathJax (nếu có công thức)
      if (typeof MathJax !== "undefined" && MathJax.typeset) {
        MathJax.typeset();
      }
    });
  }

  // === 4. Tự hiện iframe khi nhận postMessage từ bảng ===
  window.addEventListener("message", function (event) {
    if (event.data && event.data.type === "mc_select_row") {
      if (iframe && iframe.style.display === "none") {
        iframe.style.display = "block";
        if (btnViewList) btnViewList.textContent = "Ẩn danh sách";
      }
    }
  });

  // === 5. (Tuỳ chọn mở rộng) Các nút như Lưu, Xóa có thể viết thêm ở đây ===
});
