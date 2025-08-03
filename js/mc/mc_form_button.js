  // Nút "Ẩn/hiện danh sách" (#mc_view_list)
  document.getElementById('mc_view_list').addEventListener('click', () => {
    const wrapper = document.getElementById('mcTableWrapper');
    wrapper.style.display = (wrapper.style.display === 'none' || !wrapper.style.display)
      ? 'block'
      : 'none';
  });
  
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

  document.addEventListener("DOMContentLoaded", function () {
    const deleteBtn = document.getElementById("mc_delete_btn");
  
    if (deleteBtn) {
      deleteBtn.addEventListener("click", function () {
        const mcIdInput = document.getElementById("mc_id");
        const mc_id = mcIdInput ? mcIdInput.value.trim() : "";
      
        const publicIdInput = document.querySelector('input[name="existing_public_id"]');
        const public_id = publicIdInput ? publicIdInput.value.trim() : "";
      
        if (!mc_id) {
          alert("Vui lòng chọn dòng cần xoá.");
          return;
        }
      
        if (!confirm("Bạn có chắc chắn muốn xoá dòng này?")) {
          return;
        }
      
        fetch("../../includes/mc/mc_fetch_data.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            action: "delete",
            delete_mc_id: mc_id,
            public_id: public_id
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert("✅ Đã xoá thành công!");
            clearFormFields();
      
            const iframe = document.getElementById("mcTableFrame");
            if (iframe && iframe.contentWindow) {
              iframe.contentWindow.postMessage({ action: "reload_table" }, "*");
            }
          } else {
            alert("❌ Xoá thất bại: " + (data.message || "Không rõ lỗi."));
          }
        })
        .catch(error => {
          console.error("Lỗi xoá dòng:", error);
          alert("❌ Đã xảy ra lỗi khi xoá dòng.");
        });
      });

    }
  
    function clearFormFields() {
      const form = document.querySelector("form");
      if (form) form.reset();
  
      const preview = document.getElementById("mc_preview_image");
      if (preview) {
        preview.innerHTML = '';
        preview.style.display = 'none';
      }
  
      const idInput = document.getElementById('mc_id');
      if (idInput) idInput.remove();
  
      document.querySelectorAll('.preview-box').forEach(div => {
        div.innerHTML = '';
        div.style.display = 'none';
      });
      const mcPreview = document.getElementById('mcPreview');
      if (mcPreview) mcPreview.style.display = 'none';
  
      const previewContent = document.getElementById('mcPreviewContent');
      if (previewContent) previewContent.innerHTML = '';
    }
  });
  
  //  Xử lý lưu

    document.getElementById('mc_save_btn').addEventListener('click', async () => {
    const formData = new FormData(document.getElementById('mc_form'));
  
    // Lấy ID từ input ẩn (nếu có)
    const mc_id = document.getElementById('mc_id').value.trim();
    const action = mc_id ? 'update' : 'insert';
    formData.append('action', action);
  
    if (mc_id) {
      formData.append('mc_id', mc_id); // gửi ID nếu cập nhật
    }
  
    try {
      const res = await fetch('../../includes/mc/mc_fetch_data.php', {
        method: 'POST',
        body: formData
      });
  
      const data = await res.json();
  
      if (data.success) {
        alert('✅ Lưu thành công');
  
        // Reset form nếu thêm mới
        if (action === 'insert') {
          document.getElementById('mc_form').reset();
          document.getElementById('mc_id').value = '';
          document.getElementById('mc_preview_image').innerHTML = '';
        }
  
        // Gửi message cho iframe để reload bảng
        const iframe = document.getElementById('mcTableFrame');
        if (iframe && iframe.contentWindow) {
          iframe.contentWindow.postMessage({ type: 'reload_table' }, '*');
        }
  
      } else {
        alert(data.error || '❌ Lỗi khi lưu');
      }
    } catch (err) {
      console.error('❌ Fetch error:', err);
      alert('❌ Không thể kết nối máy chủ');
    }
  });
  