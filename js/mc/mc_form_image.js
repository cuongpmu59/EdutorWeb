/*
  Script upload/nén/xóa ảnh - Phiên bản tối ưu
  Yêu cầu: jQuery (đã có trong project)
  Thay apiUrl tương ứng với endpoint server của bạn.
*/

const apiUrl = '../../includes/mc/mc_form_image.php';
const MAX_WIDTH = 1200;
const MAX_HEIGHT = 1200;
const QUALITY = 0.8;

let uploadedPublicId = null;   // public_id trả về từ server (nếu có)
let isProcessing = false;      // lock để tránh nhiều request cùng lúc

// ---------- HỖ TRỢ UI ----------
function setStatus(text = '', color = '#333') {
  $('#statusMsg').css('color', color).html(text);
}

function setProcessing(on) {
  isProcessing = on;
  $('#mc_clear_image').prop('disabled', on);
  $('#mc_image').prop('disabled', on);
}

function updateNoImageText() {
  const hasImage = Boolean($('#mc_preview_image').attr('src'));
  $('#noImageText').toggle(!hasImage);
}

function resetPreview() {
  $('#mc_preview_image').attr('src', '').hide();
  $('#mc_image').val('');
  uploadedPublicId = null;
  setStatus('');
  updateNoImageText();
}

// ---------- LẤY public_id TỪ URL (dự phòng) ----------
function getPublicIdFromUrl(url) {
  // Dự phòng: nếu server không trả public_id, cố parse từ URL cloudinary
  try {
    const u = new URL(url, location.origin);
    const parts = u.pathname.split('/').filter(Boolean);
    const uploadIdx = parts.indexOf('upload');
    if (uploadIdx === -1) return null;
    let tail = parts.slice(uploadIdx + 1); // phần sau 'upload'
    // Bỏ biến thể/transforms (chứa dấu phẩy) nếu có
    if (tail.length && tail[0].includes(',')) tail.shift();
    // Bỏ version nếu có v12345
    if (tail.length && /^v\d+$/.test(tail[0])) tail.shift();
    if (!tail.length) return null;
    const filename = tail.pop();
    if (!filename.includes('.')) return tail.join('/');
    const nameWithoutExt = filename.substring(0, filename.lastIndexOf('.'));
    return [...tail, nameWithoutExt].join('/');
  } catch (e) {
    return null;
  }
}

// ---------- NÉN ẢNH (trả về Promise<Blob>) ----------
function compressImage(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onerror = () => reject(new Error('FileReader error'));
    reader.onload = () => {
      const img = new Image();
      img.onerror = () => reject(new Error('Image load error'));
      img.onload = () => {
        let width = img.width;
        let height = img.height;

        // Giữ tỉ lệ
        if (width > MAX_WIDTH || height > MAX_HEIGHT) {
          if (width / height > MAX_WIDTH / MAX_HEIGHT) {
            height = Math.round(height * (MAX_WIDTH / width));
            width = MAX_WIDTH;
          } else {
            width = Math.round(width * (MAX_HEIGHT / height));
            height = MAX_HEIGHT;
          }
        }

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, width, height);

        // canvas.toBlob là không đồng bộ; bọc trong Promise
        canvas.toBlob(blob => {
          if (blob && blob.size > 0) resolve(blob);
          else reject(new Error('Failed to compress'));
        }, 'image/jpeg', QUALITY);
      };
      img.src = reader.result;
    };
    reader.readAsDataURL(file);
  });
}

// ---------- UPLOAD (fetch với FormData) ----------
async function uploadBlob(blob, filename) {
  const fd = new FormData();
  fd.append('action', 'upload');
  // server mong muốn tên file có ext .jpg sau khi chuyển sang jpeg
  fd.append('file', blob, filename.replace(/\.[^/.]+$/, '.jpg'));

  // dùng fetch để dễ await, nhưng trả về JSON giống với $.ajax
  const resp = await fetch(apiUrl, {
    method: 'POST',
    body: fd,
    credentials: 'same-origin'
  });

  if (!resp.ok) {
    const text = await resp.text().catch(() => '');
    throw new Error('HTTP ' + resp.status + ' ' + text);
  }
  const json = await resp.json().catch(() => null);
  if (!json) throw new Error('Invalid JSON response');
  return json;
}

// ---------- DELETE ----------
async function deleteByPublicId(public_id) {
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('public_id', public_id);

  const resp = await fetch(apiUrl, {
    method: 'POST',
    body: fd,
    credentials: 'same-origin'
  });
  if (!resp.ok) {
    const text = await resp.text().catch(() => '');
    throw new Error('HTTP ' + resp.status + ' ' + text);
  }
  const json = await resp.json().catch(() => null);
  if (!json) throw new Error('Invalid JSON response');
  return json;
}

// ---------- EVENT: chọn file (upload) ----------
$(document).on('change', '#mc_image', async function () {
  const file = this.files && this.files[0];
  if (!file) return;

  if (isProcessing) return;
  setProcessing(true);
  setStatus('⏳ Nén ảnh...', '#333');

  try {
    // Nếu đã có ảnh đã upload trước đó, tự động hỏi xóa hay giữ
    if (uploadedPublicId) {
      const keep = confirm('Bạn đã upload một ảnh trước đó. Bạn muốn xóa ảnh cũ trước khi thay ảnh mới? (OK = xóa cũ)');
      if (keep) {
        setStatus('⏳ Đang xóa ảnh cũ...', '#333');
        const delRes = await deleteByPublicId(uploadedPublicId);
        if (delRes.result === 'ok') {
          resetPreview();
          setStatus('🗑 Ảnh cũ đã xóa. Tiếp tục upload mới...', 'green');
        } else {
          // nếu xóa thất bại thì ngưng upload để tránh rác
          setStatus('❌ Không thể xóa ảnh cũ — hủy upload mới.', 'red');
          setProcessing(false);
          return;
        }
      } else {
        // người dùng muốn giữ cũ -> hủy hành động select mới
        setStatus('⚠️ Đã hủy upload mới, giữ ảnh cũ.', '#333');
        setProcessing(false);
        return;
      }
    }

    // Nén
    const compressedBlob = await compressImage(file);
    // Hiển thị preview tạm (local)
    const previewReader = new FileReader();
    previewReader.onload = e => {
      $('#mc_preview_image').attr('src', e.target.result).show();
      updateNoImageText();
    };
    previewReader.readAsDataURL(compressedBlob);

    setStatus('⏳ Đang upload ảnh...', '#333');

    // Upload
    const res = await uploadBlob(compressedBlob, file.name);

    if (res && (res.secure_url || res.url)) {
      const secureUrl = res.secure_url || res.url;
      $('#mc_preview_image').attr('src', secureUrl).show();
      // ưu tiên server trả public_id
      uploadedPublicId = res.public_id || getPublicIdFromUrl(secureUrl) || null;
      setStatus('✅ Upload thành công!', 'green');
    } else {
      // server không trả url => báo lỗi
      resetPreview();
      setStatus('❌ Upload thất bại (server trả về dữ liệu không hợp lệ).', 'red');
    }

  } catch (err) {
    console.error(err);
    resetPreview();
    setStatus('❌ Lỗi: ' + (err.message || 'Không xác định'), 'red');
  } finally {
    setProcessing(false);
  }
});

// ---------- EVENT: xóa ảnh ----------
$(document).on('click', '#mc_clear_image', async function () {
  if (isProcessing) return;
  const currentSrc = $('#mc_preview_image').attr('src');
  if (!currentSrc && !uploadedPublicId) {
    setStatus('❌ Không có ảnh để xóa.', 'red');
    return;
  }

  // ưu tiên dùng uploadedPublicId nếu có
  let public_id = uploadedPublicId || getPublicIdFromUrl(currentSrc);
  if (!public_id) {
    setStatus('❌ Không thể lấy public_id để xóa.', 'red');
    return;
  }

  if (!confirm('Bạn có chắc muốn xóa ảnh này?')) return;

  setProcessing(true);
  setStatus('⏳ Đang xóa ảnh...', '#333');

  try {
    const res = await deleteByPublicId(public_id);
    if (res && res.result === 'ok') {
      resetPreview();
      setStatus('🗑 Ảnh đã được xóa.', 'green');
    } else {
      setStatus('❌ Xóa thất bại (server trả về lỗi).', 'red');
    }
  } catch (err) {
    console.error(err);
    setStatus('❌ Lỗi khi xóa: ' + (err.message || 'Không xác định'), 'red');
  } finally {
    setProcessing(false);
  }
});

// ---------- INIT ----------
$(document).ready(function () {
  updateNoImageText();
  // Nếu bạn muốn khôi phục ảnh đã upload khi mở page (ví dụ edit form),
  // server có thể gắn sẵn giá trị vào input hidden hoặc tag img; bạn có thể đọc
  // và populate uploadedPublicId nếu server còn cung cấp public_id.
  // Ví dụ (nếu bạn có <input type="hidden" id="existing_public_id" value="...">):
  const existing = $('#existing_public_id').val();
  if (existing) uploadedPublicId = existing;
});

