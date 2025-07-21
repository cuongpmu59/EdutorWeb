window.addEventListener("message", function (event) {
    if (!event.data || event.data.type !== "mc_select_row") return;
  
    const data = event.data.data;
    if (!data) return;
  
    // Gán dữ liệu vào form
    document.getElementById("mc_id")?.value = data.id || '';
    document.getElementById("mc_topic").value = data.topic || '';
    document.getElementById("mc_question").value = data.question || '';
  
    document.getElementById("mc_answer1").value = data.answer1 || '';
    document.getElementById("mc_answer2").value = data.answer2 || '';
    document.getElementById("mc_answer3").value = data.answer3 || '';
    document.getElementById("mc_answer4").value = data.answer4 || '';
  
    document.getElementById("mc_correct_answer").value = data.correct || '';
  
    // Xử lý ảnh minh hoạ
    const imagePreview = document.querySelector(".mc-image-preview");
    const existingInput = document.querySelector('input[name="existing_image"]');
  
    if (data.image) {
      imagePreview.innerHTML = `<img src="${data.image}" alt="Hình minh hoạ">`;
      if (existingInput) {
        existingInput.value = data.image;
      } else {
        const hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = "existing_image";
        hidden.value = data.image;
        document.getElementById("mcForm").appendChild(hidden);
      }
    } else {
      imagePreview.innerHTML = '';
      if (existingInput) existingInput.remove();
    }
  
    // Làm mới MathJax (nếu cần)
    if (window.MathJax) MathJax.typesetPromise();
  
  }, false);
  