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

  //   Xử lý lưu

    document.getElementById('mc_save').addEventListener('click', function () {
    const formData = new FormData();

    // Lấy dữ liệu từ form
    const mc_id = document.getElementById('mc_id')?.value || '';
    const mc_topic = document.getElementById('mc_topic')?.value.trim();
    const mc_question = document.getElementById('mc_question')?.value.trim();
    const mc_answer1 = document.getElementById('mc_answer1')?.value.trim();
    const mc_answer2 = document.getElementById('mc_answer2')?.value.trim();
    const mc_answer3 = document.getElementById('mc_answer3')?.value.trim();
    const mc_answer4 = document.getElementById('mc_answer4')?.value.trim();
    const mc_correct_answer = document.getElementById('mc_correct_answer')?.value.trim();

    // Kiểm tra dữ liệu bắt buộc
    if (!mc_question ||!mc_topic||!mc_answer1||!mc_answer2||!mc_answer3||!mc_answer4||!mc_correct_answer) {
        alert('⚠️ Vui lòng nhập đầy đủ câu hỏi và đáp án đúng.');
        return;
    }

    // Gắn vào FormData
    formData.append('mc_id', mc_id);
    formData.append('mc_topic', mc_topic);
    formData.append('mc_question', mc_question);
    formData.append('mc_answer1', mc_answer1);
    formData.append('mc_answer2', mc_answer2);
    formData.append('mc_answer3', mc_answer3);
    formData.append('mc_answer4', mc_answer4);
    formData.append('mc_correct_answer', mc_correct_answer);

    fetch('../../includes/mc/mc_form_save.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(msg => {
        alert(msg);

        // Sau khi lưu, reload bảng
        const frame = document.getElementById('mcTableFrame');
        if (frame && frame.contentWindow) {
            frame.contentWindow.location.reload();
        }

        // Reset form
        const resetBtn = document.getElementById('mc_reset');
        if (resetBtn) resetBtn.click();
    })
    .catch(err => {
        alert('❌ Lỗi khi lưu: ' + err);
    });
});
  
  // Nút "Ẩn/hiện danh sách" (#mc_view_list)
    document.getElementById('mc_view_list').addEventListener('click', () => {
    const wrapper = document.getElementById('mcTableWrapper');
    wrapper.style.display = (wrapper.style.display === 'none' || !wrapper.style.display)
      ? 'block'
      : 'none';
  });
