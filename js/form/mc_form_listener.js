window.addEventListener("message", function (event) {
    if (event.data?.type === "mc_select_row") {
      const row = event.data.data;
      
      // Gán dữ liệu vào form tương ứng
      document.querySelector('#mc_id').value = row.id || '';
      document.querySelector('#mc_topic').value = row.topic || '';
      document.querySelector('#mc_question').value = row.question || '';
      document.querySelector('#mc_answer1').value = row.answer1 || '';
      document.querySelector('#mc_answer2').value = row.answer2 || '';
      document.querySelector('#mc_answer3').value = row.answer3 || '';
      document.querySelector('#mc_answer4').value = row.answer4 || '';
      document.querySelector('#mc_correct_answer').value = row.correct || '';
  
      if (row.image) {
        document.querySelector('#preview-image').src = row.image;
        document.querySelector('#preview-image').style.display = 'inline-block';
      } else {
        document.querySelector('#preview-image').style.display = 'none';
      }
  
      if (window.MathJax) MathJax.typesetPromise();
    }
  });
  