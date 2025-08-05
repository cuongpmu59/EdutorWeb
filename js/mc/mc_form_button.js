document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('mcForm');
  const formElement = document.getElementById('mc_form'); // dùng cho FormData
  const previewImg = document.getElementById('mc_preview_image');
  const tableFrame = document.getElementById('mcTableFrame');
  const previewBox = document.querySelectorAll('.preview-box');

  // 🔁 Hàm reset form
  function clearFormFields() {
    form.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');
    form.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);

    if (previewImg) {
      previewImg.src = '';
      previewImg.style.display = 'none';
    }

    const imageInput = form.querySelector('#mc_image');
    if (imageInput) imageInput.value = '';

    ['existing_image', 'existing_public_id', 'mc_id'].forEach(name => {
      const input = form.querySelector(`input[name="${name}"]`);
      if (input) input.remove();
    });

    previewBox.forEach(div => {
      div.innerHTML = '';
      div.style.display = 'none';
    });

    const previewContainer = document.getElementById('mcPreview');
    if (previewContainer) previewContainer.style.display = 'none';

    const previewContent = document.getElementById('mcPreviewContent');
    if (previewContent) previewContent.innerHTML = '';

    if (window.MathJax && window.MathJax.typeset) {
      MathJax.typeset(); // render lại công thức toán
    }
  }

  // 🔁 Gửi tín hiệu reload iframe bảng
  function reloadTableFrame() {
    if (tableFrame?.contentWindow) {
      tableFrame.contentWindow.postMessage({ action: 'reload_table' }, '*');
    }
  }

  // ✅ Nút Ẩn/Hiện danh sách
  document.getElementById('mc_view_list')?.addEventListener('click', () => {
    const wrapper = document.getElementById('mcTableWrapper');
    wrapper.style.display = (wrapper.style.display === 'none' || !wrapper.style.display)
      ? 'block'
      : 'none';
  });

  // ✅ Nút Làm lại
  document.getElementById('mc_reset')?.addEventListener('click', clearFormFields);

  // ✅ Nút Xoá
  document.getElementById('mc_delete_btn')?.addEventListener('click', async () => {
    const btn = document.getElementById('mc_delete_btn');
    const mc_id = document.getElementById('mc_id')?.value;

    if (!mc_id) return alert('⚠️ Vui lòng chọn một dòng để xoá');

    if (!confirm('Bạn có chắc chắn muốn xoá không?')) return;

    btn.disabled = true;
    btn.textContent = 'Đang xoá...';

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
        reloadTableFrame();
      } else {
        alert(data.error || '❌ Lỗi khi xoá');
      }

    } catch (err) {
      console.error('❌ Fetch error:', err);
      alert('❌ Không thể kết nối máy chủ');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Xoá';
    }
  });

  // ✅ Nút Lưu
  document.getElementById('mc_save_btn')?.addEventListener('click', async () => {
    const formData = new FormData(formElement);
    const fileInput = document.getElementById('mc_image');

    if (fileInput?.files.length > 0) {
      formData.append('image', fileInput.files[0]);
    }

    const isUpdate = formData.get('mc_id') !== '';
    formData.append('action', isUpdate ? 'update' : 'create');

    try {
      const res = await fetch('../../includes/mc/mc_fetch_data.php', {
        method: 'POST',
        body: formData
      });

      const result = await res.json();

      if (result.success) {
        alert(result.success);
        reloadTableFrame();
        if (!isUpdate) clearFormFields();
      } else {
        alert(result.error || '❌ Có lỗi xảy ra!');
      }

    } catch (err) {
      console.error('❌ Lỗi lưu:', err);
      alert('❌ Không thể lưu dữ liệu');
    }
  });
});
