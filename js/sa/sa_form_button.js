// Nút "Làm mới" (#sa_reset)
document.getElementById('sa_reset').addEventListener('click', function () {
    const form = document.getElementById('saForm');
  
    form.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');
    form.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);
    
    const img = document.getElementById('sa_preview_image');
    if (img) {
      img.src = '';
      img.style.display = 'none';
    }
  
    const imageInput = form.querySelector('#sa_image');
    if (imageInput) imageInput.value = '';
  
    const hiddenImage = form.querySelector('input[name="existing_image"]');
    if (hiddenImage) hiddenImage.remove();
  
    document.querySelectorAll('.preview-box').forEach(div => {
      div.innerHTML = '';
      div.style.display = 'none';
    });
    document.getElementById('saPreview').style.display = 'none';
    document.getElementById('saPreviewContent').innerHTML = '';
  
    if (window.MathJax && window.MathJax.typeset) {
      MathJax.typeset();
    }
  
    const idInput = document.getElementById('sa_id');
    if (idInput) idInput.remove();
  });
  
  // Nút "Xoá" (#sa_delete)
  document.getElementById('sa_delete').addEventListener('click', async function () {
    const idInput = document.getElementById('sa_id');
    if (!idInput) {
      alert('⚠️ Không có câu hỏi nào để xoá.');
      return;
    }
  
    const sa_id = idInput.value.trim();
    if (!sa_id) {
      alert('⚠️ ID câu hỏi không hợp lệ.');
      return;
    }
  
    if (!confirm('❌ Bạn có chắc muốn xoá câu hỏi này?')) return;
  
    const deleteBtn = this;
    deleteBtn.disabled = true;
    deleteBtn.textContent = 'Đang xoá...';
  
    try {
      const res = await fetch('../../includes/sa/sa_form_delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ sa_id })
      });
  
      const data = await res.json();
  
      if (data.success) {
        alert(data.message);
        document.getElementById('sa_reset')?.click();
        const frame = document.getElementById('saTableFrame');
        if (frame?.contentWindow) {
          frame.contentWindow.location.reload(true);
        }
      } else {
        alert(data.message);
      }
  
    } catch (err) {
      alert('❌ Lỗi khi xoá: ' + err);
    } finally {
      deleteBtn.disabled = false;
      deleteBtn.textContent = 'Xoá';
    }
  });
  
  // Xử lý lưu
  document.getElementById('sa_save')?.addEventListener('click', async () => {
    const formData = new FormData();
    const getVal = id => document.getElementById(id)?.value.trim() || '';
    const requiredFields = ['sa_topic', 'sa_question', 'sa_correct_answer'];
  
    for (const field of requiredFields) {
      if (!getVal(field)) {
        alert('⚠️ Vui lòng nhập đầy đủ câu hỏi và đáp án.');
        return;
      }
    }
  
    ['sa_id', ...requiredFields].forEach(id => {
      formData.append(id, getVal(id));
    });
    formData.append('sa_image_url', getVal('sa_image_url'));
  
    try {
      const res = await fetch('../../includes/sa/sa_form_save.php', {
        method: 'POST',
        body: formData
      });
  
      const data = await res.json();
      alert(data.message);
      if (data.status === 'success') {
        document.getElementById('saTableFrame')?.contentWindow?.location.reload();
        document.getElementById('sa_reset')?.click();
      }
    } catch (err) {
      alert('❌ Lỗi khi lưu: ' + err.message);
    }
  });
  
  // Nút "Ẩn/hiện danh sách" (#sa_view_list)
  document.getElementById('sa_view_list').addEventListener('click', () => {
    const wrapper = document.getElementById('saTableWrapper');
    wrapper.style.display = (wrapper.style.display === 'none' || !wrapper.style.display)
      ? 'block'
      : 'none';
  });
  