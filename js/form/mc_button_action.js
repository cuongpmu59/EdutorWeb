    // Nút "Làm lại" (#mc_reset)

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

  //Nút "Xoá" (#mc_delete)

  document.getElementById('mc_delete').addEventListener('click', function () {
    const idInput = document.getElementById('mc_id');
    if (!idInput) {
      alert('⚠️ Không có câu hỏi nào để xoá.');
      return;
    }
  
    const mc_id = idInput.value;
  
    if (!confirm('❌ Bạn có chắc muốn xoá câu hỏi này? Hành động này không thể hoàn tác.')) return;
  
    fetch('../../includes/mc_delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ mc_id })
    })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
  
      const resetBtn = document.getElementById('mc_reset');
      if (resetBtn) resetBtn.click();
  
      const frame = document.getElementById('mcTableFrame');
      if (frame && frame.contentWindow) {
        frame.contentWindow.location.reload();
      }
    })
    .catch(err => {
      alert('❌ Lỗi khi xoá: ' + err);
    });
  });
  
//   Xử lý lưu

document.getElementById('mc_save').addEventListener('click', async function () {
  const form = document.getElementById('mcForm');
  const formData = new FormData(form);

  const mc_id = form.querySelector('#mc_id')?.value ?? '';

  const requiredFields = ['mc_topic', 'mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer'];

  // Nếu là thêm mới, bắt buộc điền đủ
  if (!mc_id) {
    for (const field of requiredFields) {
      if (!form[field].value.trim()) {
        alert('❌ Vui lòng nhập đầy đủ thông tin cho các trường bắt buộc!');
        return;
      }
    }
  }

  // Xử lý ảnh nếu có ảnh mới
  const imageFile = form.mc_image.files[0];
  if (imageFile) {
    const cloudData = new FormData();
    cloudData.append('file', imageFile);
    cloudData.append('upload_preset', 'YOUR_PRESET');  // Thay bằng preset của bạn

    try {
      const cloudRes = await fetch('https://api.cloudinary.com/v1_1/YOUR_CLOUD_NAME/image/upload', {
        method: 'POST',
        body: cloudData
      });

      const cloudResult = await cloudRes.json();

      if (cloudResult.error) {
        alert('❌ Lỗi Cloudinary: ' + cloudResult.error.message);
        return;
      }

      formData.append('mc_image_url', cloudResult.secure_url);
      formData.append('public_id', cloudResult.public_id);
    } catch (err) {
      alert('❌ Không thể tải ảnh lên Cloudinary.');
      return;
    }
  }

  // Gửi dữ liệu đến máy chủ
  try {
    const response = await fetch('../../includes/mc_save.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();
    if (result.success) {
      alert('✅ Dữ liệu đã được lưu.');
      window.location.reload(); // hoặc cập nhật bảng nếu cần
    } else {
      alert('❌ Lỗi khi lưu: ' + result.message);
    }
  } catch (err) {
    alert('❌ Có lỗi trong quá trình gửi dữ liệu: ' + err.message);
  }
});
