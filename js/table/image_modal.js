document.addEventListener("DOMContentLoaded", function () {
    // Tạo modal ảnh nếu chưa có
    if (!document.querySelector(".image-modal")) {
      const modalHTML = `
        <div class="image-modal" id="imageModal">
          <span class="image-modal-close" id="closeImageModal">&times;</span>
          <img class="image-modal-content" id="imageModalContent">
        </div>
      `;
      document.body.insertAdjacentHTML("beforeend", modalHTML);
    }
  
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("imageModalContent");
    const closeBtn = document.getElementById("closeImageModal");
  
    // Mở modal khi click vào ảnh có class tương ứng
    document.body.addEventListener("click", function (e) {
      const target = e.target;
      if (
        target.tagName === "IMG" &&
        (target.classList.contains("thumb") ||
          target.classList.contains("preview-image") ||
          target.id === "mc_imagePreview")
      ) {
        modal.style.display = "block";
        modalImg.src = target.src;
      }
    });
  
    // Đóng modal khi bấm nút ✖
    closeBtn.addEventListener("click", function () {
      modal.style.display = "none";
      modalImg.src = "";
    });
  
    // Đóng modal khi bấm Esc
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        modal.style.display = "none";
        modalImg.src = "";
      }
    });
  
    // Đóng modal khi click ra ngoài ảnh
    modal.addEventListener("click", function (e) {
      if (e.target === modal) {
        modal.style.display = "none";
        modalImg.src = "";
      }
    });
  });
  