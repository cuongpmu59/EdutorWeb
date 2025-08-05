// Nút "Ẩn/hiện danh sách"
document.getElementById('mc_view_list').addEventListener('click', () => {
  const wrapper = document.getElementById('mcTableWrapper');
  wrapper.style.display = (wrapper.style.display === 'none' || !wrapper.style.display)
    ? 'block'
    : 'none';
});

// Nút "Làm lại"
document.getElementById('mc_reset').addEventListener('click', function () {
  const form = document.getElementById('mcForm');
  form.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');
  form.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);

  const img = document.getElementById('mc_preview_image');
  if (img) {
    img.src = '';
    img.style.display = 'none';
  }

  const imageInput = form.querySelector('#mc_image');
  if (imageInput) imageInput.value = '';

  const hiddenImage = form.querySelector('input[name="existing_image"]');
  if (hiddenImage) hiddenImage.remove();

  const publicIdInput = document.querySelector('input[name="existing_public_id"]');
  if (publicIdInput) publicIdInput.remove();

  document.querySelectorAll('.preview-box').forEach(div => {
    div.innerHTML = '';
    div.style.display = 'none';
  });
  document.getElementById('mcPreview').style.display = 'none';
  document.getElementById('mcPreviewContent').innerHTML = '';

  if (window.MathJax && window.MathJax.typeset) {
    MathJax.typeset();
  }

  const idInput = document.getElementById('mc_id');
  if (idInput) idInput.remove();
});

// Nút "Xoá"
  document.getElementById('mc_delete_btn').addEventListener('click', async () => {
  const deleteBtn = document.getElementById('mc_delete_btn');
  deleteBtn.disabled = true;
  deleteBtn.textContent = 'Đang xoá...';

  const mc_id = document.getElementById('mc_id')?.value;
  if (!mc_id) {
    alert('⚠️ Vui lòng chọn một dòng để xoá');
    deleteBtn.disabled = false;
    deleteBtn.textContent = 'Xoá';
    return;
  }

  if (!confirm('Bạn có chắc chắn muốn xoá không?')) {
    deleteBtn.disabled = false;
    deleteBtn.textContent = 'Xoá';
    return;
  }

  try {
    const res = await fetch('../../includes/mc/mc_fetch_data.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `delete_mc_id=${encodeURIComponent(mc_id)}`
    });

    const data = await res.json();

    if (data.success) {
      alert('✅ Đã xoá thành công');
      clearFormFields();

      // Gửi tín hiệu reload bảng
      const iframe = document.getElementById('mcTableFrame');
      if (iframe?.contentWindow) {
        iframe.contentWindow.postMessage({ action: 'reload_table' }, '*');
      }

    } else {
      alert(data.error || '❌ Lỗi khi xoá');
    }

  } catch (err) {
    console.error('❌ Fetch error:', err);
    alert('❌ Không thể kết nối máy chủ');
  } finally {
    deleteBtn.disabled = false;
    deleteBtn.textContent = 'Xoá';
  }
});

// Nút "Lưu"
document.getElementById('mc_save_btn').addEventListener('click', async () => {
  const saveBtn = document.getElementById('mc_save_btn');
  saveBtn.disabled = true;
  saveBtn.textContent = 'Đang lưu...';

  const formEl = document.getElementById('mcForm');
  if (!formEl) return;

  const formData = new FormData(formEl);

  const mcIdInput = document.getElementById('mc_id');
  const mc_id = mcIdInput ? mcIdInput.value.trim() : '';
  const action = mc_id ? 'update' : 'insert';
  formData.append('action', action);
  if (mc_id) formData.append('mc_id', mc_id);

  // Kiểm tra nếu ảnh đã xoá khỏi khung preview thì gửi thêm delete_image = true
  const img = document.getElementById('mc_preview_image');
  if (img && !img.src) {
    formData.append('delete_image', 'true');
  }

  try {
    const res = await fetch('../../includes/mc/mc_fetch_data.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();

    if (data.success) {
      alert('✅ Lưu thành công');

      if (action === 'insert') {
        clearFormFields();
      }

      // Gửi tín hiệu reload bảng
      const iframe = document.getElementById('mcTableFrame');
      if (iframe?.contentWindow) {
        iframe.contentWindow.postMessage({ action: 'reload_table' }, '*');
      }

    } else {
      alert(data.error || '❌ Lỗi khi lưu');
    }
  } catch (err) {
    console.error('❌ Fetch error:', err);
    alert('❌ Không thể kết nối máy chủ');
  } finally {
    saveBtn.disabled = false;
    saveBtn.textContent = 'Lưu';
  }
});

// Hàm xoá toàn bộ các trường trong form
function clearFormFields() {
  const form = document.getElementById("mcForm");
  if (form) form.reset();

  const preview = document.getElementById("mc_preview_image");
  if (preview) {
    preview.src = '';
    preview.style.display = 'none';
  }

  const idInput = document.getElementById('mc_id');
  if (idInput) idInput.remove();

  const publicIdInput = document.querySelector('input[name="existing_public_id"]');
  if (publicIdInput) publicIdInput.remove();

  document.querySelectorAll('.preview-box').forEach(div => {
    div.innerHTML = '';
    div.style.display = 'none';
  });

  const mcPreview = document.getElementById('mcPreview');
  if (mcPreview) mcPreview.style.display = 'none';

  const previewContent = document.getElementById('mcPreviewContent');
  if (previewContent) previewContent.innerHTML = '';
}

// Xử lý nút xoá ảnh
document.getElementById('mc_delete_image_btn').addEventListener('click', () => {
  const previewImg = document.getElementById('mc_preview_image');
  const fileInput = document.getElementById('mc_image');
  const deleteFlag = document.getElementById('delete_image_flag');

  // Ẩn ảnh nếu đang có
  if (previewImg) {
    previewImg.src = '';
    previewImg.style.display = 'none';
  }

  // Reset input file
  if (fileInput) {
    fileInput.value = '';
  }

  // Gắn cờ xóa ảnh
  if (deleteFlag) {
    deleteFlag.value = 'true';
  }
});
