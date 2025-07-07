/**
 * Hàm viết ngắn cho document.getElementById
 * @param {string} id
 * @returns {HTMLElement}
 */
export const $ = id => document.getElementById(id);

/**
 * Hàm viết ngắn cho document.querySelector
 * @param {string} selector
 * @returns {Element}
 */
export const $$ = selector => document.querySelector(selector);

/**
 * Hiển thị ảnh đã chọn vào cả tab minh hoạ và xem trước chính
 * @param {string} url - Đường dẫn ảnh Cloudinary
 * @param {string} fileName - Tên file hiển thị (tùy chọn)
 */
export function showImagePreview(url, fileName = "") {
  if (!url) return;

  $("imageTabPreview").src = url;
  $("imageTabPreview").style.display = "block";

  $("preview_image").src = url;
  $("preview_image").style.display = "block";

  $("image_url").value = url;
  $("imageTabFileName").textContent = fileName || "Đã có ảnh";
  $("delete_image_tab").style.display = "inline-block";
}
