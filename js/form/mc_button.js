document.getElementById('mc_save').addEventListener('click', () => {
    const form = document.getElementById('mcForm');
    const data = new FormData(form);
    const url = form.mc_id ? 'api/update_mc_question.php' : 'api/create_mc_question.php';
    fetch(url, { method: 'POST', body: data })
      .then(res => res.json())
      .then(res => {
        alert(res.message);
        if (res.mc_id) location.href = `mc_form.php?mc_id=${res.mc_id}`;
      });
  });
  
  document.getElementById('mc_delete').addEventListener('click', () => {
    const mcId = document.getElementById('mc_id')?.value;
    if (!mcId) return alert('Chưa chọn mục để xóa');
    if (confirm('Bạn có chắc muốn xóa?')) {
      fetch('api/delete_mc_question.php', {
        method: 'POST',
        body: JSON.stringify({ mc_id: mcId }),
        headers: {'Content-Type': 'application/json'}
      }).then(res => res.json()).then(r => {
        alert(r.message);
        if (r.deleted) location.href = 'mc_list.php';
      });
    }
  });
  
  document.getElementById('mc_reset').addEventListener('click', () => {
    document.getElementById('mcForm').reset();
    document.querySelector('.mc-image-preview').innerHTML = '';
  });
  
  document.getElementById('mc_preview_exam').addEventListener('click', () => {
    // chuyển hướng hoặc mở màn hình preview đầy đủ đề
    window.open(`mc_exam_preview.php?mc_id=${document.getElementById('mc_id')?.value || ''}`, '_blank');
  });
  