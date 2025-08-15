document.addEventListener('DOMContentLoaded', () => {

  const form = document.getElementById('mcForm');
  const resetBtn = document.getElementById('mc_reset');
  const deleteBtn = document.getElementById('mc_delete');
  const saveBtn = document.getElementById('mc_save');
  const viewListBtn = document.getElementById('mc_view_list');
  const tableWrapper = document.getElementById('mcTableWrapper');
  const tableFrame = document.getElementById('mcTableFrame');
  const previewToggle = document.getElementById('mcTogglePreview');
  const mcPreview = document.getElementById('mcPreview');
  const mcPreviewContent = document.getElementById('mcPreviewContent');

  // ================== Hàm tiện ích ==================
  const getVal = id => document.getElementById(id)?.value.trim() || '';
  const showAlert = msg => alert(msg);

  const updateTableHeight = () => {
    const formHeight = document.getElementById('formContainer').offsetHeight;
    document.documentElement.style.setProperty('--form-height', formHeight + 'px');
    if (tableWrapper.classList.contains('show')) {
      tableFrame.style.height = (window.innerHeight - formHeight) + 'px';
    }
  };

  window.addEventListener('resize', updateTableHeight);
  window.addEventListener('load', updateTableHeight);

  // ================== Nút "Làm mới" ==================
  resetBtn?.addEventListener('click', () => {
    form.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');
    form.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);

    const img = document.getElementById('mc_preview_image');
    if (img) { img.src = ''; img.style.display = 'none'; }

    const imageInput = form.querySelector('#mc_image');
    if (imageInput) imageInput.value = '';

    const hiddenImage = form.querySelector('input[name="existing_image"]');
    if (hiddenImage) hiddenImage.remove();

    document.querySelectorAll('.preview-box').forEach(div => {
      div.innerHTML = '';
      div.style.display = 'none';
    });

    mcPreview.style.display = 'none';
    mcPreviewContent.innerHTML = '';

    if (window.MathJax?.typeset) MathJax.typeset();

    const idInput = document.getElementById('mc_id');
    if (idInput) idInput.value = '';
  });

  // ================== Nút "Xoá" ==================
  deleteBtn?.addEventListener('click', async () => {
    const mc_id = getVal('mc_id');
    if (!mc_id) return showAlert('⚠️ Không có câu hỏi nào để xoá.');

    if (!confirm('❌ Bạn có chắc muốn xoá câu hỏi này?')) return;

    deleteBtn.disabled = true;
    deleteBtn.textContent = 'Đang xoá...';

    try {
      const res = await fetch('../../includes/mc/mc_form_delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ mc_id })
      });

      const data = await res.json();
      showAlert(data.message);

      if (data.success) {
        resetBtn.click();
        tableFrame.contentWindow?.location.reload(true);
      }
    } catch (err) {
      showAlert('❌ Lỗi khi xoá: ' + err);
    } finally {
      deleteBtn.disabled = false;
      deleteBtn.textContent = 'Xoá';
    }
  });

  // ================== Nút "Lưu" ==================
  saveBtn?.addEventListener('click', async e => {
    e.preventDefault();
    const formData = new FormData();
    const requiredFields = ['mc_topic','mc_question','mc_answer1','mc_answer2','mc_answer3','mc_answer4','mc_correct_answer'];

    for (const field of requiredFields) {
      if (!getVal(field)) return showAlert('⚠️ Vui lòng nhập đầy đủ câu hỏi và đáp án.');
    }

    ['mc_id', ...requiredFields].forEach(id => formData.append(id, getVal(id)));
    formData.append('mc_image_url', getVal('mc_image_url'));

    try {
      const res = await fetch('../../includes/mc/mc_form_save.php', {
        method: 'POST',
        body: formData
      });

      const data = await res.json();
      showAlert(data.message);

      if (data.status === 'success') {
        tableFrame.contentWindow?.location.reload();
        resetBtn.click();
      }
    } catch (err) {
      showAlert('❌ Lỗi khi lưu: ' + err.message);
    }
  });

  // ================== Nút "Ẩn/Hiện danh sách" ==================
  viewListBtn?.addEventListener('click', () => {
    tableWrapper.classList.toggle('show');
    updateTableHeight();
    if (tableWrapper.classList.contains('show')) tableWrapper.scrollIntoView({ behavior: 'smooth' });
  });

  // ================== Nút "Xem trước toàn bộ" ==================
  previewToggle?.addEventListener('click', () => {
    if (mcPreview.style.display === 'none') {
      mcPreviewContent.innerHTML = `
        <strong>Chủ đề:</strong> ${getVal('mc_topic')}<br>
        <strong>Câu hỏi:</strong> ${getVal('mc_question')}<br>
        <strong>A:</strong> ${getVal('mc_answer1')}<br>
        <strong>B:</strong> ${getVal('mc_answer2')}<br>
        <strong>C:</strong> ${getVal('mc_answer3')}<br>
        <strong>D:</strong> ${getVal('mc_answer4')}<br>
        <strong>Đáp án đúng:</strong> ${getVal('mc_correct_answer')}<br>
        ${getVal('mc_image_url') ? `<img src="${getVal('mc_image_url')}" style="max-width:200px;">` : ''}
      `;
      mcPreview.style.display = 'block';
      if (window.MathJax?.typeset) MathJax.typeset();
    } else {
      mcPreview.style.display = 'none';
    }
  });

  // ================== Nhận dữ liệu từ iframe ==================
  window.addEventListener('message', event => {
    const { type, data } = event.data || {};
    if (type !== 'fill-form' || !data) return;

    ['mc_id','mc_topic','mc_question','mc_answer1','mc_answer2','mc_answer3','mc_answer4','mc_correct_answer'].forEach(id => {
      $('#'+id).val(data[id] || '');
    });

    if (data.mc_image_url) {
      $('#mc_preview_image').attr('src', data.mc_image_url).show();
      $('#mc_image_url').val(data.mc_image_url);
    } else {
      $('#mc_preview_image').hide().attr('src','');
      $('#mc_image_url').val('');
    }

    window.scrollTo({ top:0, behavior:'smooth' });
  });

});
