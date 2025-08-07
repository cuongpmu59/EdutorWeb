document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('mcForm');
  const formElement = document.getElementById('mc_form'); // Dùng cho FormData
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
      MathJax.typeset(); // Render lại công thức toán học
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

    if (!mc_id) return alert('⚠️ Vui lòng chọn một dòng để xoá.');

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
        alert('✅ Đã xoá thành công.');
        clearFormFields();
        reloadTableFrame();
      } else {
        alert(data.error || '❌ Lỗi khi xoá.');
      }

    } catch (err) {
      console.error('❌ Lỗi fetch:', err);
      alert('❌ Không thể kết nối đến máy chủ.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Xoá';
    }
  });

  // ✅ Nút Lưu ảnh
  document.getElementById('mc_save_image').addEventListener('click', async function () {
    const file = document.getElementById('mc_image').files[0];
    const mc_id = document.getElementById('mc_id').value;
    const preview = document.getElementById('mc_preview_image');
  
    if (!file || !mc_id) {
      alert('❌ Vui lòng chọn ảnh và có ID.');
      return;
    }
  
    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload_preset', 'YOUR_UPLOAD_PRESET');
  
    try {
      const res = await fetch('https://api.cloudinary.com/v1_1/YOUR_CLOUD_NAME/image/upload', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();
  
      if (data.secure_url) {
        // Hiển thị ảnh xem trước
        preview.src = data.secure_url;
        preview.style.display = 'block';
  
        // Gửi URL về server PHP để lưu vào CSDL
        const saveRes = await fetch('../../includes/mc/save_uploaded_image.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
            mc_id: mc_id,
            image_url: data.secure_url
          })
        });
  
        const saveResult = await saveRes.json();
        if (saveResult.success) {
          alert('✅ Đã lưu ảnh vào cơ sở dữ liệu.');
          document.getElementById('mcTableFrame')?.contentWindow?.location.reload();
        } else {
          alert('❌ ' + (saveResult.error || 'Lỗi khi lưu ảnh.'));
        }
      } else {
        alert('❌ Tải ảnh lên thất bại.');
      }
  
    } catch (err) {
      console.error(err);
      alert('❌ Có lỗi xảy ra khi tải ảnh.');
    }
  });

  // ✅ Nút Xoá ảnh
  document.getElementById('mc_clear_image').addEventListener('click', async function () {
    const previewImg = document.getElementById('mc_preview_image');
    const imageUrl = previewImg.src;

    if (!imageUrl || imageUrl.includes('default') || imageUrl === window.location.href) {
      alert("❌ Không có ảnh nào để xoá.");
      return;
    }

    try {
      const response = await fetch('includes/mc/delete_cloudinary_image.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ image_url: imageUrl })
      });

      const result = await response.json();

      if (result.success) {
        alert("✅ Ảnh đã được xoá khỏi Cloudinary.");

        // 1. Xoá ảnh khỏi phần xem trước
        previewImg.src = '';

        // 2. Reset input file
        document.getElementById('mc_image').value = '';

        // 3. Ẩn các nút liên quan
        document.getElementById('mc_clear_image').style.display = 'none';
        document.getElementById('mc_save_image').style.display = 'none';

        // 4. Cập nhật lại iframe chứa bảng mc_table.php
        const iframe = document.getElementById('mcTableFrame');
        if (iframe) {
          iframe.contentWindow.location.reload();
        }

      } else {
        alert("❌ Lỗi khi xoá ảnh: " + (result.error || 'Không rõ nguyên nhân.'));
      }

    } catch (error) {
      console.error('Lỗi khi gọi API xoá ảnh:', error);
      alert("❌ Không thể kết nối đến máy chủ.");
    }
  });

});
