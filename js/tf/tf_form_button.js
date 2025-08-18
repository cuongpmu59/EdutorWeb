// Nút "Làm mới" (#tf_reset)
document.getElementById('tf_reset').addEventListener('click', function () {
  const form = document.getElementById('tfForm');

  // Xóa text, textarea
  form.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');
  form.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);

  // Reset radio Đúng/Sai
  form.querySelectorAll('input[type="radio"]').forEach(radio => radio.checked = false);

  // Ẩn ảnh preview
  const img = document.getElementById('tf_preview_image');
  if (img) {
    img.src = '';
    img.style.display = 'none';
  }

  const imageInput = form.querySelector('#tf_image');
  if (imageInput) imageInput.value = '';

  document.getElementById('tf_image_url').value = '';

  // Reset preview box
  document.querySelectorAll('.preview-box').forEach(div => {
    div.innerHTML = '';
    div.style.display = 'none';
  });
  document.getElementById('tfPreview').style.display = 'none';
  document.getElementById('tfPreviewContent').innerHTML = '';

  if (window.MathJax && window.MathJax.typeset) {
    MathJax.typeset();
  }

  document.getElementById('tf_id').value = '';
});


// Nút "Xoá" (#tf_delete)
document.getElementById('tf_delete').addEventListener('click', async function () {
  const tf_id = document.getElementById('tf_id').value.trim();
  if (!tf_id) {
    alert('⚠️ Không có câu hỏi nào để xoá.');
    return;
  }

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


// Nút "Lưu" (#tf_save)
document.getElementById('tf_save')?.addEventListener('click', async () => {
  const formData = new FormData();
  const getVal = id => document.getElementById(id)?.value.trim() || '';

  // Bắt buộc nhập
  const requiredFields = [
    'tf_topic',
    'tf_question',
    'tf_statement1', 'tf_statement2', 'tf_statement3', 'tf_statement4'
  ];

  for (const field of requiredFields) {
    if (!getVal(field)) {
      alert('⚠️ Vui lòng nhập đầy đủ câu hỏi và các mệnh đề.');
      return;
    }
  }

  // Bắt buộc chọn Đúng/Sai cho từng mệnh đề
  for (let i = 1; i <= 4; i++) {
    const radios = document.querySelectorAll(`input[name="correct_answer${i}"]`);
    if (![...radios].some(r => r.checked)) {
      alert(`⚠️ Vui lòng chọn Đúng/Sai cho mệnh đề ${i}.`);
      return;
    }
  }

  // Gom dữ liệu vào formData
  ['tf_id', ...requiredFields].forEach(id => {
    formData.append(id, getVal(id));
  });
  formData.append('tf_image_url', getVal('tf_image_url'));

  for (let i = 1; i <= 4; i++) {
    const checked = document.querySelector(`input[name="correct_answer${i}"]:checked`);
    if (checked) {
      formData.append(`tf_correct_answer${i}`, checked.value);
    }
  }

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


// // Nút "Ẩn/hiện danh sách" (#tf_view_list)
// document.getElementById('tf_view_list').addEventListener('click', () => {
//   const wrapper = document.getElementById('tfTableWrapper');
//   wrapper.style.display = (wrapper.style.display === 'none' || !wrapper.style.display)
//     ? 'block'
//     : 'none';
// });

const btnToggle = document.getElementById('tf_view_list');
const wrapper = document.getElementById('tfTableWrapper');

if (btnToggle && wrapper) {
  btnToggle.addEventListener('click', () => {
    const isHidden = wrapper.style.display === 'none' || getComputedStyle(wrapper).display === 'none';
    wrapper.style.display = isHidden ? 'block' : 'none';
    btnToggle.textContent = isHidden ? 'Ẩn danh sách' : 'Hiện danh sách';
  });
}

