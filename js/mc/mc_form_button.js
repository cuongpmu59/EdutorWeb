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
  const form = document.getElementById('mc_form');
  const formData = new FormData(form);

  // Thêm ảnh nếu có
  const fileInput = document.getElementById('mc_image');
  if (fileInput.files.length > 0) {
    formData.append('image', fileInput.files[0]);
  }

  // Nếu có mc_id thì cập nhật, không thì thêm mới
  const isUpdate = formData.get('mc_id') !== '';

  formData.append('action', isUpdate ? 'update' : 'create');

  try {
    const response = await fetch('../../includes/mc/mc_fetch_data.php', {
      method: 'POST',
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      alert(result.success);
      // Reset hoặc cập nhật lại bảng
      const frame = document.getElementById('mcTableFrame');
      frame.contentWindow.location.reload(); // Cập nhật lại iframe
      if (!isUpdate) form.reset();
    } else {
      alert(result.error || '❌ Có lỗi xảy ra!');
    }
  } catch (err) {
    console.error('Lỗi lưu:', err);
    alert('❌ Không thể lưu dữ liệu');
  }
});
