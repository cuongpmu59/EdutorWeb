    // Nút "Làm mới" (#mc_reset)

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
  
    document.getElementById('mc_delete').addEventListener('click', async function () {
      const idInput = document.getElementById('mc_id');
      if (!idInput) {
        alert('⚠️ Không có câu hỏi nào để xoá.');
        return;
      }
    
      const mc_id = idInput.value.trim();
      if (!mc_id) {
        alert('⚠️ ID câu hỏi không hợp lệ.');
        return;
      }
    
      if (!confirm('❌ Bạn có chắc muốn xoá câu hỏi này?')) return;
    
      const deleteBtn = this;
      deleteBtn.disabled = true;
      deleteBtn.textContent = 'Đang xoá...';
    
      try {
        const res = await fetch('../../includes/mc/mc_form_delete.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ mc_id })
        });
    
        const data = await res.json();
    
        if (data.success) {
          alert(data.message);
          document.getElementById('mc_reset')?.click();
          const frame = document.getElementById('mcTableFrame');
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
    document.getElementById('mc_save')?.addEventListener('click', async () => {
    const formData = new FormData();
    const getVal = id => document.getElementById(id)?.value.trim() || '';
    const requiredFields = [
        'mc_topic', 'mc_question',
        'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4',
        'mc_correct_answer'
    ];
  
    for (const field of requiredFields) {
        if (!getVal(field)) {
            alert('⚠️ Vui lòng nhập đầy đủ câu hỏi và đáp án.');
            return;
        }
    }
  
     ['mc_id', ...requiredFields].forEach(id => {
        formData.append(id, getVal(id));
    });
        formData.append('mc_image_url', getVal('mc_image_url'));
  
    try {
        const res = await fetch('../../includes/mc/mc_form_save.php', {
            method: 'POST',
            body: formData
        });
  
        const data = await res.json();
        alert(data.message);
        if (data.status === 'success') {
            document.getElementById('mcTableFrame')?.contentWindow?.location.reload();
            document.getElementById('mc_reset')?.click();
        }
    } catch (err) {
        alert('❌ Lỗi khi lưu: ' + err.message);
    }
  });
  
    // Nút "Ẩn/hiện danh sách" (#mc_view_list)
    const btn = document.getElementById('mc_view_list');
const wrapper = document.getElementById('mcTableWrapper');
const form = document.getElementById('formContainer');
const iframe = document.getElementById('mcTableFrame');

function updateTableHeight() {
  const formHeight = form.offsetHeight;
  wrapper.style.top = formHeight + 'px';
  iframe.style.height = (window.innerHeight - formHeight) + 'px';
}

// Nút ẩn/hiện danh sách
btn.addEventListener('click', () => {
  wrapper.classList.toggle('show');
  if (wrapper.classList.contains('show')) {
    updateTableHeight();
  }
});

// Cập nhật khi resize hoặc load
window.addEventListener('resize', () => {
  if (wrapper.classList.contains('show')) {
    updateTableHeight();
  }
});
window.addEventListener('load', () => {
  if (wrapper.classList.contains('show')) {
    updateTableHeight();
  }
});
    