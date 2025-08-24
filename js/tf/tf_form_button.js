// ================== tf_form_button.js ==================

// ==== Nút "Làm mới" (#tf_reset) ====
document.getElementById('tf_reset')?.addEventListener('click', function () {
  const form = document.getElementById('tfForm');

  // Reset text + textarea
  form.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');

  // Reset select
  form.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);

  // Reset radio + checkbox
  form.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(el => el.checked = false);

  // Reset ảnh preview
  const img = document.getElementById('tf_preview_image');
  if (img) {
    img.src = '';
    img.style.display = 'none';
  }

  // Reset preview box
  form.querySelectorAll('.preview-box').forEach(box => box.style.display = 'none');
});


// ==== Nút "Lưu" (#tf_save) ====
document.getElementById('tf_save')?.addEventListener('click', async () => {
  const formData = new FormData();

  // Hàm lấy giá trị (tự động xử lý text/textarea/select/radio/checkbox)
  const getVal = nameOrId => {
    const el = document.getElementById(nameOrId);
    if (el) {
      return el.value.trim();
    }
    // Nếu không tìm thấy theo id -> thử lấy radio/checkbox theo name
    const checked = document.querySelector(`input[name="${nameOrId}"]:checked`);
    if (checked) return checked.value;
    return '';
  };

  // Các trường bắt buộc
  const requiredFields = [
    'tf_topic', 'tf_question',
    'tf_statement1', 'tf_statement2', 'tf_statement3', 'tf_statement4',
    'correct_answer1', 'correct_answer2', 'correct_answer3', 'correct_answer4'
  ];

  // Validate
for (const field of requiredFields) {
  const val = getVal(field);
  if (val === '') {   // chỉ khi rỗng mới báo lỗi
    alert('⚠️ Vui lòng nhập đầy đủ thông tin câu hỏi và đáp án.');
    return;
  }
}

  // Append dữ liệu
  [
    'tf_id', 'tf_topic', 'tf_question',
    'tf_statement1', 'tf_statement2', 'tf_statement3', 'tf_statement4',
    'correct_answer1', 'correct_answer2', 'correct_answer3', 'correct_answer4'
  ].forEach(name => {
    formData.append(name, getVal(name));
  });

  // Ảnh (không bắt buộc)
  formData.append('tf_image_url', getVal('tf_image_url'));

  try {
    const res = await fetch('../../includes/tf/tf_form_save.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();
    alert(data.message);

    if (data.status === 'success') {
      // Reload bảng
      document.getElementById('tfTableFrame')?.contentWindow?.location.reload();
      // Reset form
      document.getElementById('tf_reset')?.click();
    }
  } catch (err) {
    alert('❌ Lỗi khi lưu: ' + err.message);
  }
});

// ========================================================
