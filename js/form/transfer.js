// === Nhận dữ liệu từ iframe: chọn dòng → điền vào form ===
window.addEventListener('message', function (event) {
    if (event.data?.type === 'mc_selected_row') {
      const d = event.data.data;
  
      // Điền các trường văn bản vào form
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
  
      // Hiển thị ảnh minh hoạ nếu có
      const img = document.getElementById('mc_imagePreview');
      if (d.mc_image_url) {
        img.src = d.mc_image_url;
        img.style.display = 'block';
      } else {
        img.removeAttribute('src'); // tránh giữ lại ảnh cũ
        img.style.display = 'none';
      }
  
      // Cập nhật lại preview công thức Toán (MathJax)
      if (typeof renderMathPreviewAll === 'function') {
        renderMathPreviewAll();
      }
  
      // Cuộn lên đầu form
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  });
  
  // === Gửi yêu cầu từ form cha đến iframe: chuyển tab "Danh sách" ===
  function scrollToListTabInIframe() {
    const iframe = document.getElementById('mcIframe');
    if (iframe?.contentWindow) {
      iframe.contentWindow.postMessage({ type: 'scrollToListTab' }, '*');
    }
  }
  
  // Cho phép gọi từ HTML trực tiếp trong onclick
  window.scrollToListTabInIframe = scrollToListTabInIframe;
  