// Lắng nghe sự kiện message từ iframe mc_table.php
window.addEventListener("message", function (event) {
    if (event.data?.type === "mc_select_row") {
      const row = event.data.data;
  
      // Gán các trường dữ liệu từ dòng được chọn
      document.querySelector('#mc_id')?.value = row.id || '';
      document.querySelector('#mc_topic')?.value = row.topic || '';
      document.querySelector('#mc_question')?.value = row.question || '';
  
      ['1', '2', '3', '4'].forEach(i => {
        const input = document.querySelector('#mc_answer' + i);
        if (input) input.value = row['answer' + i] || '';
      });
  
      const answer = document.querySelector('#mc_answer');
      if (answer) answer.value = row.correct || '';
  
      // Cập nhật ảnh minh họa
      const imgPreview = document.querySelector('.mc-image-preview');
      if (imgPreview) {
        if (row.image) {
          imgPreview.innerHTML = `<img src="${row.image}" alt="Hình minh hoạ">`;
  
          // Tạo hoặc cập nhật hidden input để lưu URL ảnh hiện tại
          let input = document.querySelector('input[name="existing_image"]');
          if (!input) {
            input = document.createElement("input");
            input.type = "hidden";
            input.name = "existing_image";
            document.querySelector('#mcForm').appendChild(input);
          }
          input.value = row.image;
        } else {
          imgPreview.innerHTML = '';
          const input = document.querySelector('input[name="existing_image"]');
          if (input) input.remove();
        }
      }
  
      // Làm mới MathJax sau khi cập nhật nội dung
      if (window.MathJax) MathJax.typesetPromise();
    }
  });
  