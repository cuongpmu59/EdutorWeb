// Danh sách các trường liên quan
const previewFields = [
    { id: 'tf_question', label: 'Câu hỏi' },
    { id: 'tf_statement1', label: 'Mệnh đề 1' },
    { id: 'tf_statement2', label: 'Mệnh đề 2' },
    { id: 'tf_statement3', label: 'Mệnh đề 3' },
    { id: 'tf_statement4', label: 'Mệnh đề 4' }
  ];
  
  // Cập nhật preview cho từng trường riêng lẻ (kèm radio đúng/sai)
  function updatePreview(id) {
    const inputEl = document.getElementById(id);
    const previewEl = document.getElementById(`preview-${id}`);
    if (!inputEl || !previewEl) return;
  
    const value = inputEl.value.trim();
    let html = value;
  
    // Nếu là mệnh đề => thêm radio đúng/sai
    if (id.startsWith('tf_statement')) {
      const radios = document.querySelectorAll(`input[name="${id}_ans"]`);
      let selected = '';
      radios.forEach(r => {
        if (r.checked) selected = r.value === 'true' ? '✔ Đúng' : '✘ Sai';
      });
      html += selected ? ` &nbsp; <em>(${selected})</em>` : '';
    }
  
    previewEl.innerHTML = html;
  
    if (window.MathJax) {
      MathJax.typesetPromise([previewEl]);
    }
  }
  
  // Cập nhật toàn bộ nội dung
  function updateFullPreview() {
    const topic = document.getElementById('tf_topic')?.value.trim() || '';
    let html = `<p><strong>Chủ đề:</strong> ${topic}</p>`;
  
    previewFields.forEach(({ id, label }) => {
      const value = document.getElementById(id)?.value.trim() || '';
      let radioHtml = '';
  
      if (id.startsWith('tf_statement')) {
        const radios = document.querySelectorAll(`input[name="${id}_ans"]`);
        let selected = '';
        radios.forEach(r => {
          if (r.checked) selected = r.value === 'true' ? '✔ Đúng' : '✘ Sai';
        });
        radioHtml = selected ? ` <em>(${selected})</em>` : '';
      }
  
      html += `<p><strong>${label}: </strong> ${value}${radioHtml}</p>`;
    });
  
    const fullPreviewEl = document.getElementById('tfPreviewContent');
    if (fullPreviewEl) {
      fullPreviewEl.innerHTML = html;
      if (window.MathJax) {
        MathJax.typesetPromise([fullPreviewEl]);
      }
    }
  }
  
  // Thiết lập sự kiện input realtime
  function setupRealtimePreview() {
    previewFields.forEach(({ id }) => {
      const inputEl = document.getElementById(id);
      if (inputEl) {
        inputEl.addEventListener('input', () => {
          updatePreview(id);
          updateFullPreview();
        });
      }
  
      // Thêm listener cho radio đúng/sai
      const radios = document.querySelectorAll(`input[name="${id}_ans"]`);
      radios.forEach(r => {
        r.addEventListener('change', () => {
          updatePreview(id);
          updateFullPreview();
        });
      });
    });
  
    const topicEl = document.getElementById('tf_topic');
    if (topicEl) {
      topicEl.addEventListener('input', updateFullPreview);
    }
  }
  
  // Toggle preview từng trường
  function setupPreviewToggle() {
    const toggleButtons = document.querySelectorAll('.toggle-preview');
    toggleButtons.forEach(button => {
      const targetId = button.getAttribute('data-target');
      const previewEl = document.getElementById(`preview-${targetId}`);
  
      button.addEventListener('click', () => {
        if (!previewEl) return;
        const isVisible = window.getComputedStyle(previewEl).display !== 'none';
        previewEl.style.display = isVisible ? 'none' : 'block';
  
        if (!isVisible) {
          updatePreview(targetId);
        }
      });
    });
  }
  
  // Toggle preview toàn bộ
  function setupFullPreviewToggle() {
    const btn = document.getElementById('tfTogglePreview');
    const zone = document.getElementById('tfPreview');
  
    if (btn && zone) {
      btn.addEventListener('click', () => {
        const isVisible = window.getComputedStyle(zone).display !== 'none';
  
        if (isVisible) {
          zone.style.display = 'none';
        } else {
          zone.style.display = 'flex';
          updateFullPreview();
        }
      });
    }
  }
  
  // Khởi tạo
  document.addEventListener('DOMContentLoaded', () => {
    setupRealtimePreview();
    setupPreviewToggle();
    setupFullPreviewToggle();
  });
  