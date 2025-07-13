// js/table/button.js

document.addEventListener("DOMContentLoaded", () => {
    const addBtn = document.getElementById("btnAddQuestion");
    const reloadBtn = document.getElementById("btnReloadTable");
  
    if (addBtn) {
      addBtn.addEventListener("click", () => {
        // Gửi thông điệp về form cha để mở form thêm mới
        if (window.parent !== window) {
          window.parent.postMessage({ action: "addMcQuestion" }, "*");
        }
      });
    }
  
    if (reloadBtn) {
      reloadBtn.addEventListener("click", () => {
        location.reload();
      });
    }
  });
  