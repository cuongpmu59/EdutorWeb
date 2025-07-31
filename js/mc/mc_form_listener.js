window.addEventListener('message', function (e) {
  if (e.data?.type === 'fill-form') {
    const data = e.data.data;

    document.querySelector('#mc_id').value = data.mc_id || '';
    document.querySelector('#mc_topic').value = data.mc_topic || '';
    document.querySelector('#mc_question').value = data.mc_question || '';
    document.querySelector('#mc_answer1').value = data.mc_answer1 || '';
    document.querySelector('#mc_answer2').value = data.mc_answer2 || '';
    document.querySelector('#mc_answer3').value = data.mc_answer3 || '';
    document.querySelector('#mc_answer4').value = data.mc_answer4 || '';
    document.querySelector('#mc_correct_answer').value = data.mc_correct_answer || '';

    const img = document.querySelector('#mc_preview_image');
    if (data.mc_image_url) {
      img.src = data.mc_image_url;
      img.style.display = 'block';
    } else {
      img.src = '';
      img.style.display = 'none';
    }
  }
});

// window.addEventListener('message', function (event) {
//   // Bảo vệ – bạn có thể kiểm tra origin nếu muốn
//   if (!event.data || event.data.type !== 'fill-form') return;

//   const data = event.data.data;

//   // Gán dữ liệu vào form
//   document.getElementById('mc_id').value = data.mc_id || '';
//   document.getElementById('mc_topic').value = data.mc_topic || '';
//   document.getElementById('mc_question').value = data.mc_question || '';
//   document.getElementById('mc_answer1').value = data.mc_answer1 || '';
//   document.getElementById('mc_answer2').value = data.mc_answer2 || '';
//   document.getElementById('mc_answer3').value = data.mc_answer3 || '';
//   document.getElementById('mc_answer4').value = data.mc_answer4 || '';
//   document.getElementById('mc_correct_answer').value = data.mc_correct_answer || '';

//   // Hiển thị ảnh nếu có
//   const preview = document.getElementById('mc_preview_image');
//   preview.innerHTML = '';

//   if (data.mc_image_url) {
//     const img = document.createElement('img');
//     img.src = data.mc_image_url;
//     img.alt = 'Ảnh';
//     img.style.maxWidth = '120px';
//     img.style.marginTop = '8px';
//     preview.appendChild(img);
//   }
// });


