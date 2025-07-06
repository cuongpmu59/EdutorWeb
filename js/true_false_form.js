document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".tab-button");
    const frame = document.getElementById("contentFrame");
  
    buttons.forEach((btn) => {
      btn.addEventListener("click", () => {
        // Đổi trạng thái tab
        buttons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
  
        // Đổi nội dung iframe theo trang tương ứng
        const targetSrc = btn.getAttribute("data-src");
        frame.src = targetSrc;
      });
    });
  });
  