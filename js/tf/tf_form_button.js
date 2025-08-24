// ========================
// Nút "Làm mới" (#tf_reset)
// ========================
document.getElementById('tf_reset').addEventListener('click', function () {
  const form = document.getElementById('tfForm');

  // Reset text + textarea
  form.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');
  form.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);

  // Reset radio (Đúng/Sai)
  form.querySelectorAll('input[type="radio"]').forEach(r => r.checked = false);

  // Reset ảnh
  const img = document.getElementById('tf_preview_image');
  if (img) {
    img.src = '';
    img.style.display = 'none';
  }
  const imageInput = form.querySelector('#tf_image');
  if (imageInput) imageInput.value = '';
  const hiddenImage = document.getElementById('tf_image_url');
  if (hiddenImage) hiddenImage.value = '';

  // Reset preview
  document.querySelectorAll('.preview-box').forEach(div => {
    div.innerHTML = '';
    div.style.display = 'none';
  });
  document.getElementById('tfPreview').style.display = 'none';
  document.getElementById('tfPreviewContent').innerHTML = '';

  if (window.MathJax && window.MathJax.typeset) {
    MathJax.typeset();
  }

  // Xóa id
  const idInput = document.getElementById('tf_id');
  if (idInput) idInput.value = '';
});

// ========================
// Nút "Xóa" (#tf_delete)
// ========================
document.getElementById('tf_delete').addEventListener('click', async function () {
  const idInput = document.getElementById('tf_id');
  if (!idInput || !idInput.value.trim()) {
    alert('⚠️ Không có câu hỏi nào để xoá.');
    return;
  }

  const tf_id = idInput.value.trim();
  if (!confirm('❌ Bạn có chắc muốn xoá câu hỏi này?')) return;

  const deleteBtn = this;
  deleteBtn.disabled = true;
  deleteBtn.textContent = 'Đang xoá...';

  try {
    const res = await fetch('../../includes/tf/tf_form_delete.php', {
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

// ========================
// Nút "Lưu" (#tf_save)
// ========================
document.getElementById('tf_save')?.addEventListener('click', async () => {
  const formData = new FormData();
  const getVal = id => document.getElementById(id)?.value.trim() || '';

  // Các trường bắt buộc
  const requiredFields = ['tf_topic', 'tf_question'];
  for (let i = 1; i <= 4; i++) {
    requiredFields.push(`tf_statement${i}`);
  }

  for (const field of requiredFields) {
    if (!getVal(field)) {
      alert('⚠️ Vui lòng nhập đầy đủ thông tin câu hỏi và mệnh đề.');
      return;
    }
  }

  // Kiểm tra radio cho từng statement
  for (let i = 1; i <= 4; i++) {
    const radios = document.querySelectorAll(`input[name="tf_correct_answer${i}"]`);
    if (![...radios].some(r => r.checked)) {
      alert(`⚠️ Vui lòng chọn Đúng/Sai cho mệnh đề ${i}.`);
      return;
    }
  }

  // Append dữ liệu
  formData.append('tf_id', getVal('tf_id'));
  formData.append('tf_topic', getVal('tf_topic'));
  formData.append('tf_question', getVal('tf_question'));

  for (let i = 1; i <= 4; i++) {
    formData.append(`tf_statement${i}`, getVal(`tf_statement${i}`));
    const checked = document.querySelector(`input[name="tf_correct_answer${i}"]:checked`);
    formData.append(`tf_correct_answer${i}`, checked ? checked.value : '');
  }

  formData.append('tf_image_url', getVal('tf_image_url'));

  try {
    const res = await fetch('../../includes/tf/tf_form_save.php', {
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

// ========================
// Nút "Ẩn/hiện danh sách"
// ========================
document.getElementById('tf_view_list').addEventListener('click', () => {
  const wrapper = document.getElementById('tfTableWrapper');
  wrapper.style.display = (wrapper.style.display === 'none' || !wrapper.style.display)
    ? 'block'
    : 'none';
});
