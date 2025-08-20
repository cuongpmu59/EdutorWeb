// ==== Nút "Làm mới" (#tf_reset) ====
document.getElementById('tf_reset').addEventListener('click', function () {
  const form = document.getElementById('tfForm');

  form.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');
  form.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);

  const img = document.getElementById('tf_preview_image');
  if (img) {
    img.src = '';
    img.style.display = 'none';
  }

  const imageInput = form.querySelector('#tf_image');
  if (imageInput) imageInput.value = '';

  const hiddenImage = form.querySelector('input[name="existing_image"]');
  if (hiddenImage) hiddenImage.remove();

  document.querySelectorAll('.preview-box').forEach(div => {
    div.innerHTML = '';
    div.style.display = 'none';
  });
  document.getElementById('tfPreview').style.display = 'none';
  document.getElementById('tfPreviewContent').innerHTML = '';

  if (window.MathJax && window.MathJax.typeset) {
    MathJax.typeset();
  }

  const idInput = document.getElementById('tf_id');
  if (idInput) idInput.remove();

  // Xóa highlight lỗi
  form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
});


// ==== Nút "Xoá" (#tf_delete) ====
document.getElementById('tf_delete').addEventListener('click', async function () {
  const idInput = document.getElementById('tf_id');
  if (!idInput) {
    alert('⚠️ Không có câu hỏi nào để xoá.');
    return;
  }

  const tf_id = idInput.value.trim();
  if (!tf_id) {
    alert('⚠️ ID câu hỏi không hợp lệ.');
    return;
  }

  if (!confirm('❌ Bạn có chắc muốn xoá câu hỏi này?')) return;

  const deleteBtn = this;
  deleteBtn.disabled = true;
  deleteBtn.textContent = 'Đang xoá...';

  try {
    const res = await fetch('../../includes/tf/tf_delete.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ tf_id })
    });

    const data = await res.json();

    if (data.success) {
      alert(data.message);
      document.getElementById('tf_reset')?.click();
      const frame = document.getElementById('tfTableFrame');
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


// ==== Nút "Lưu" (#tf_save) ====
document.getElementById('tf_save')?.addEventListener('click', async () => {
  const formData = new FormData();
  const getVal = id => document.getElementById(id)?.value.trim() || '';

  // Các trường bắt buộc
  const requiredFields = [
    'tf_topic', 'tf_question',
    'tf_statement1', 'tf_statement2', 'tf_statement3', 'tf_statement4',
    'tf_correct_answer1', 'tf_correct_answer2', 'tf_correct_answer3', 'tf_correct_answer4'
  ];

  let valid = true;
  requiredFields.forEach(field => {
    const el = document.getElementById(field);
    if (!getVal(field)) {
      el?.classList.add('input-error'); // highlight thiếu
      valid = false;
    } else {
      el?.classList.remove('input-error');
    }
  });

  if (!valid) {
    alert('⚠️ Vui lòng nhập đầy đủ tất cả các trường.');
    return;
  }

  ['tf_id', ...requiredFields].forEach(id => {
    formData.append(id, getVal(id));
  });
  formData.append('tf_image_url', getVal('tf_image_url'));

  try {
    const res = await fetch('../../includes/tf/tf_save.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();
    alert(data.message);
    if (data.status === 'success') {
      document.getElementById('tfTableFrame')?.contentWindow?.location.reload();
      document.getElementById('tf_reset')?.click();
    }
  } catch (err) {
    alert('❌ Lỗi khi lưu: ' + err.message);
  }
});


// ==== Nút "Ẩn/hiện danh sách" (#tf_view_list) ====
document.getElementById('tf_view_list').addEventListener('click', () => {
  const wrapper = document.getElementById('tfTableWrapper');
  wrapper.style.display = (wrapper.style.display === 'none' || !wrapper.style.display)
    ? 'block'
    : 'none';
});
