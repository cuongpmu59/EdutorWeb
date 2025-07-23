document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const mc_id = urlParams.get('mc_id');
    if (mc_id) {
      fetch(`../../includes/get_mc_question.php?mc_id=${mc_id}`)
        .then(res => res.json())
        .then(data => {
          if (data && data.mc_id) {
            fillFormWithData(data);
          }
        });
    }
  });
  
  function fillFormWithData(mc) {
    document.getElementById('mc_topic').value = mc.mc_topic || '';
    document.getElementById('mc_question').value = mc.mc_question || '';
    for (let i = 1; i <= 4; i++) {
      document.getElementById(`mc_answer${i}`).value = mc[`mc_answer${i}`] || '';
    }
    document.getElementById('mc_correct_answer').value = mc.mc_correct_answer || 'A';
  
    if (mc.mc_image_url) {
      const imgPreview = document.querySelector('.mc-image-preview');
      imgPreview.innerHTML = `<img src="${mc.mc_image_url}" alt="Hình minh hoạ">`;
  
      const hiddenInput = document.createElement('input');
      hiddenInput.type = 'hidden';
      hiddenInput.name = 'existing_image';
      hiddenInput.value = mc.mc_image_url;
      document.querySelector('#mcForm').appendChild(hiddenInput);
    }
  
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'mc_id';
    idInput.id = 'mc_id';
    idInput.value = mc.mc_id;
    document.querySelector('#mcForm').appendChild(idInput);
  }
  