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
  
  