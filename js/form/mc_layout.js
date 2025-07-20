// Load dữ liệu khi click "Xem danh sách" hoặc “Sửa”
document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('mc_view_list').addEventListener('click', function(){
      // redirect hoặc load ajax bảng danh sách
      window.location.href = 'mc_list.php';
    });
  
    // Nếu form có mc_id, load lại dữ liệu qua AJAX và set lên form
    const mcIdField = document.getElementById('mc_id');
    if (mcIdField) {
      const id = mcIdField.value;
      fetch(`api/get_mc_question.php?mc_id=${id}`)
        .then(res => res.json())
        .then(data => {
          document.getElementById('mc_topic').value = data.mc_topic;
          document.getElementById('mc_question').value = data.mc_question;
          ['A','B','C','D'].forEach(o => {
            document.getElementById(`mc_opt_${o}`).value = data[`mc_opt_${o}`];
          });
          document.getElementById('mc_answer').value = data.mc_answer;
          // set image nếu có
          if (data.mc_image_url) {
            document.querySelector('.mc-image-preview').innerHTML = `<img src="${data.mc_image_url}">`;
          }
        });
    }
  });
  