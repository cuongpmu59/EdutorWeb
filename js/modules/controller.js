// js/modules/controller.js

import * as formView from "./formView.js";
import * as imageManager from "./imageManager.js";
import * as previewView from "./previewView.js";
import * as tableView from "./tableView.js";

const container = document.getElementById("tabContent");
const buttons = document.querySelectorAll(".tab-button");

const views = {
  "pages/mc/mc_form_inner.php": formView,
  "pages/mc/mc_image.php": imageManager,
  "pages/mc/mc_preview.php": previewView,
  "pages/mc/mc_table.php": tableView
};

// Gắn sự kiện click cho từng tab
buttons.forEach(btn => {
  btn.addEventListener("click", () => {
    const url = btn.dataset.url;
    if (!url || !views[url]) return;

    // Cập nhật trạng thái active
    buttons.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");

    // Gọi render của view tương ứng
    views[url].render(container);
  });
});

// Tự động kích hoạt tab đầu tiên khi trang load
document.addEventListener("DOMContentLoaded", () => {
  const firstTab = document.querySelector(".tab-button.active") || buttons[0];
  if (firstTab) {
    firstTab.click(); // Giả lập click để kích hoạt
  }
});
