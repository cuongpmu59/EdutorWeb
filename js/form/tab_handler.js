// js/tab_handler.js

// Hàm xử lý tab chuyển đổi
function setupTabs(tabClass = ".tab-button", contentClass = ".tab-content") {
    const buttons = document.querySelectorAll(tabClass);
    const contents = document.querySelectorAll(contentClass);
  
    buttons.forEach(btn => {
      btn.addEventListener("click", () => {
        // Xóa class active khỏi tất cả
        buttons.forEach(b => b.classList.remove("active"));
        contents.forEach(c => c.classList.remove("active"));
  
        // Thêm class active vào tab được chọn
        btn.classList.add("active");
        const targetId = btn.dataset.tab || btn.dataset.subtab;
        const target = document.getElementById(targetId);
        if (target) target.classList.add("active");
      });
    });
  }
  
  // Hàm khởi tạo khi DOM sẵn sàng
  export function initTabs() {
    setupTabs(".tab-button", ".tab-content");
    setupTabs(".subtab-button", ".subtab-content");
  }
  