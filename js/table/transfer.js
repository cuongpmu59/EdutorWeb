// Nhận dữ liệu từ iframe để điền vào form
window.addEventListener('message', function (event) {
  console.log("📥 Đã nhận từ iframe:", event.data);
  
  if (event.data?.type === 'mc_selected_row') {
    const d = event.data.data;
    const fields = [
      'mc_id',
      'mc_topic',
      'mc_question',
      'mc_answer1',
      'mc_answer2',
      'mc_answer3',
      'mc_answer4',
      'mc_correct_answer'
    ];
    fields.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = d[id] || '';
    });

    const img = document.getElementById('mc_imagePreview');
    if (d.mc_image_url) {
      img.src = d.mc_image_url;
      img.style.display = 'block';
    } else {
      img.removeAttribute('src');
      img.style.display = 'none';
    }

    if (typeof renderMathPreviewAll === 'function') {
      renderMathPreviewAll();
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});

// Gửi yêu cầu chuyển tab danh sách sang iframe
function scrollToListTabInIframe() {
  const iframe = document.getElementById('mcIframe');
  if (iframe?.contentWindow) {
    iframe.contentWindow.postMessage({ type: 'scrollToListTab' }, '*');
  }
}
window.scrollToListTabInIframe = scrollToListTabInIframe;
