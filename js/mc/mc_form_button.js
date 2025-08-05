    // // Nút "Làm lại" (#mc_reset)
    //   document.getElementById('mc_reset').addEventListener('click', function () {
    //   const form = document.getElementById('mcForm');
  
    //   form.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');
    //   form.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);
      
    //   const img = document.getElementById('mc_preview_image');
    //   if (img) {
    //     img.src = '';
    //     img.style.display = 'none';
    //   }
    
    //   const imageInput = form.querySelector('#mc_image');
    //   if (imageInput) imageInput.value = '';
    
    //   const hiddenImage = form.querySelector('input[name="existing_image"]');
    //   if (hiddenImage) hiddenImage.remove();
    
    //   document.querySelectorAll('.preview-box').forEach(div => {
    //     div.innerHTML = '';
    //     div.style.display = 'none';
    //   });
    //   document.getElementById('mcPreview').style.display = 'none';
    //   document.getElementById('mcPreviewContent').innerHTML = '';
    
    //   if (window.MathJax && window.MathJax.typeset) {
    //     MathJax.typeset();
    //   }
  
    //   const idInput = document.getElementById('mc_id');
    //   if (idInput) idInput.remove();
    // });
  
    // //Nút "Xoá" (#mc_delete)
  
    // document.getElementById('mc_delete').addEventListener('click', function () {
    //   const idInput = document.getElementById('mc_id');
    //   if (!idInput) {
    //     alert('⚠️ Không có câu hỏi nào để xoá.');
    //     return;
    //   }
    
    //   const mc_id = idInput.value;
    
    //   if (!confirm('❌ Bạn có chắc muốn xoá câu hỏi này? Hành động này không thể hoàn tác.')) return;
    
    //   fetch('../../includes/mc_delete.php', {
    //     method: 'POST',
    //     headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    //     body: new URLSearchParams({ mc_id })
    //   })
    //   .then(res => res.text())
    //   .then(msg => {
    //     alert(msg);
    
    //     const resetBtn = document.getElementById('mc_reset');
    //     if (resetBtn) resetBtn.click();
    
    //     const frame = document.getElementById('mcTableFrame');
    //     if (frame && frame.contentWindow) {
    //       frame.contentWindow.location.reload();
    //     }
    //   })
    //   .catch(err => {
    //     alert('❌ Lỗi khi xoá: ' + err);
    //   });
    // });
    
    // //   Xử lý lưu
  
    //   document.getElementById('mc_save').addEventListener('click', async function () {
    //   const form = document.getElementById('mcForm');
    //   const formData = new FormData(form);
    //   const mc_id = form.querySelector('#mc_id')?.value ?? '';
    
    //   const requiredFields = ['mc_topic', 'mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer'];
    
    //   // Nếu là thêm mới thì yêu cầu nhập đầy đủ
    //   if (!mc_id) {
    //     for (const field of requiredFields) {
    //       if (!form[field].value.trim()) {
    //         alert('❌ Vui lòng nhập đầy đủ thông tin cho các trường bắt buộc!');
    //         return;
    //       }
    //     }
    //   }
    
    //   const imageFile = form.mc_image.files[0];
    //   const existingImage = form.querySelector('input[name="existing_image"]')?.value;
    
    //   if (imageFile) {
    //     // Nếu có ảnh mới, upload lên Cloudinary
    //     const cloudData = new FormData();
    //     cloudData.append('file', imageFile);
    //     cloudData.append('upload_preset', 'YOUR_PRESET'); // Thay bằng preset thật
    
    //     try {
    //       const cloudRes = await fetch('https://api.cloudinary.com/v1_1/YOUR_CLOUD_NAME/image/upload', {
    //         method: 'POST',
    //         body: cloudData
    //       });
    
    //       const cloudResult = await cloudRes.json();
    
    //       if (cloudResult.error) {
    //         alert('❌ Lỗi Cloudinary: ' + cloudResult.error.message);
    //         return;
    //       }
    
    //       formData.append('mc_image_url', cloudResult.secure_url);
    //       formData.append('public_id', cloudResult.public_id); // Nếu bạn muốn lưu thêm ID
    //     } catch (err) {
    //       alert('❌ Không thể tải ảnh lên Cloudinary.');
    //       return;
    //     }
    //   } else if (existingImage) {
    //     // Nếu không có ảnh mới, nhưng có ảnh cũ → giữ lại ảnh cũ
    //     formData.append('mc_image_url', existingImage);
    //   } else if (!mc_id) {
    //     // Nếu là thêm mới và không có ảnh nào → có thể cảnh báo hoặc cho phép tiếp tục tuỳ bạn
    //     alert('❌ Vui lòng chọn ảnh minh hoạ.');
    //     return;
    //   }
    
    //   // Gửi dữ liệu về server
    //   try {
    //     const response = await fetch('../../includes/mc_save.php', {
    //       method: 'POST',
    //       body: formData
    //     });
    
    //     const result = await response.json();
    
    //     if (result.success) {
    //       alert('✅ Dữ liệu đã được lưu.');
    //       window.location.reload(); // hoặc cập nhật bảng
    //     } else {
    //       alert('❌ Lỗi khi lưu: ' + result.message);
    //     }
    //   } catch (err) {
    //     alert('❌ Lỗi gửi dữ liệu: ' + err.message);
    //   }
    // });
    
    // Nút "Ẩn/hiện danh sách" (#mc_view_list)
    // document.getElementById('mc_view_list').addEventListener('click', () => {
    //   const wrapper = document.getElementById('mcTableWrapper');
    //   wrapper.style.display = (wrapper.style.display === 'none' || !wrapper.style.display)
    //     ? 'block'
    //     : 'none';
    // });
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

