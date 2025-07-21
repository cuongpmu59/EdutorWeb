window.addEventListener("message", function (event) {
    const msg = event.data;
    if (!msg || msg.type !== "mc_select_row") return;
  
    const data = msg.data;
  
    // === GÁN DỮ LIỆU VÀO FORM ===
    document.getElementById("mc_id").value = data.id || '';
    document.getElementById("mc_topic").value = data.topic || '';
    document.getElementById("mc_question").value = data.question || '';
  
    document.getElementById("mc_answer1").value = data.answer1 || '';
    document.getElementById("mc_answer2").value = data.answer2 || '';
    document.getElementById("mc_answer3").value = data.answer3 || '';
    document.getElementById("mc_answer4").value = data.answer4 || '';
    document.getElementById("mc_correct_answer").value = data.correct || '';
  
    // === HIỂN THỊ ẢNH MINH HOẠ ===
    const imagePreview = document.querySelector(".mc-image-preview");
    const form = document.getElementById("mcForm");
    const existingInputName = "existing_image";
  
    if (data.image) {
      imagePreview.innerHTML = `<img src="${data.image}" alt="Ảnh minh hoạ">`;
  
      let existingImageInput = form.querySelector(`input[name="${existingInputName}"]`);
      if (!existingImageInput) {
        existingImageInput = document.createElement("input");
        existingImageInput.type = "hidden";
        existingImageInput.name = existingInputName;
        form.appendChild(existingImageInput);
      }
      existingImageInput.value = data.image;
    } else {
      imagePreview.innerHTML = '';
      const existingImageInput = form.querySelector(`input[name="${existingInputName}"]`);
      if (existingImageInput) existingImageInput.remove();
    }
  
    // === CẬP NHẬT XEM TRƯỚC TOÀN BỘ (NẾU ĐANG MỞ) ===
    const fullPreview = document.querySelector(".mc-full-preview");
    if (fullPreview && fullPreview.style.display !== "none") {
      document.getElementById("preview_topic").innerText = data.topic || '';
      document.getElementById("preview_question").innerHTML = data.question || '';
      document.getElementById("preview_image").innerHTML = data.image
        ? `<img src="${data.image}" alt="Ảnh minh hoạ">`
        : '';
      document.getElementById("preview_answer1").innerHTML = data.answer1 || '';
      document.getElementById("preview_answer2").innerHTML = data.answer2 || '';
      document.getElementById("preview_answer3").innerHTML = data.answer3 || '';
      document.getElementById("preview_answer4").innerHTML = data.answer4 || '';
      document.getElementById("preview_correct").innerText = data.correct || '';
  
      if (window.MathJax) MathJax.typesetPromise();
    }
  
    // === THÊM HIỆU ỨNG CHUYỂN (flash màu) ===
    const highlight = id => {
      const el = document.getElementById(id);
      if (!el) return;
      el.classList.add('mc-highlight');
      setTimeout(() => el.classList.remove('mc-highlight'), 500);
    };
  
    ['mc_topic', 'mc_question', 'mc_answer1', 'mc_answer2', 'mc_answer3', 'mc_answer4', 'mc_correct_answer'].forEach(highlight);
  });
  