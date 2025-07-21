document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("mcTogglePreview");
    const previewZone = document.getElementById("mcPreview");
    const previewContent = document.getElementById("mcPreviewContent");
  
    if (!toggleBtn) return;
  
    toggleBtn.addEventListener("click", function () {
      const isHidden = previewZone.style.display === "none" || previewZone.style.display === "";
      
      if (isHidden) {
        const topic = document.getElementById("mc_topic").value;
        const question = document.getElementById("mc_question").value;
        const opts = ['1', '2', '3', '4'].map(opt => {
          const val = document.getElementById("mc_answer" + opt).value;
          return `<p><strong>${opt}:</strong> ${val}</p>`;
        }).join('');
        const answer = document.getElementById("mc_answer").value;
  
        let imageHtml = '';
        const imgTag = document.querySelector(".mc-image-preview img");
        if (imgTag) {
          imageHtml = `<div><strong>Hình minh hoạ:</strong><br><img src="${imgTag.src}" style="max-width: 200px;"></div>`;
        }
  
        previewContent.innerHTML = `
          <p><strong>Chủ đề:</strong> ${topic}</p>
          <p><strong>Câu hỏi:</strong> ${question}</p>
          ${opts}
          <p><strong>Đáp án đúng:</strong> ${answer}</p>
          ${imageHtml}
        `;
  
        previewZone.style.display = "block";
        toggleBtn.title = "Ẩn xem trước";
        toggleBtn.querySelector("i").classList.replace("fa-eye", "fa-eye-slash");
  
        if (window.MathJax) MathJax.typesetPromise([previewContent]);
      } else {
        previewZone.style.display = "none";
        toggleBtn.title = "Xem trước toàn bộ";
        toggleBtn.querySelector("i").classList.replace("fa-eye-slash", "fa-eye");
      }
    });
  });
  