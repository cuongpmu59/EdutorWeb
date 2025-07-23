document.addEventListener("DOMContentLoaded", () => {
  // === LẮNG NGHE postMessage TỪ mc_table.php ===
  window.addEventListener("message", function (event) {
    const msg = event.data;
    if (!msg || msg.type !== "mc_select_row") return;

    const data = msg.data;
    if (!data) return;

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
    }

    // === HIỆU ỨNG CHUYỂN NHẸ KHI ĐỔ DỮ LIỆU ===
    const formContainer = document.getElementById("mcForm");
    formContainer.classList.add("fade-highlight");
    setTimeout(() => formContainer.classList.remove("fade-highlight"), 300);

    // === CẬP NHẬT MATHJAX ===
    if (window.MathJax) MathJax.typesetPromise();

    console.log("Đã nhận dữ liệu từ bảng:", data);
  });

  // === TỰ ĐỘNG FETCH DỮ LIỆU TỪ mc_id (NẾU CÓ TRÊN URL) ===
  const urlParams = new URLSearchParams(window.location.search);
  const mc_id = urlParams.get("mc_id");
  if (mc_id) {
    fetch(`../../includes/get_mc_question.php?mc_id=${mc_id}`)
      .then((res) => res.json())
      .then((data) => {
        if (data && data.mc_id) {
          fillFormWithData(data);
          console.log("Đã lấy dữ liệu từ mc_id =", mc_id);
        }
      })
      .catch((err) => console.error("Lỗi khi fetch dữ liệu:", err));
  }

  function fillFormWithData(mc) {
    document.getElementById("mc_topic").value = mc.mc_topic || "";
    document.getElementById("mc_question").value = mc.mc_question || "";

    for (let i = 1; i <= 4; i++) {
      document.getElementById(`mc_answer${i}`).value = mc[`mc_answer${i}`] || "";
    }

    document.getElementById("mc_correct_answer").value = mc.mc_correct_answer || "A";

    // Ảnh minh hoạ
    if (mc.mc_image_url) {
      const imagePreview = document.querySelector(".mc-image-preview");
      imagePreview.innerHTML = `<img src="${mc.mc_image_url}" alt="Hình minh hoạ">`;

      const hiddenInput = document.createElement("input");
      hiddenInput.type = "hidden";
      hiddenInput.name = "existing_image";
      hiddenInput.value = mc.mc_image_url;
      document.querySelector("#mcForm").appendChild(hiddenInput);
    }

    // hidden mc_id
    const idInput = document.createElement("input");
    idInput.type = "hidden";
    idInput.name = "mc_id";
    idInput.id = "mc_id";
    idInput.value = mc.mc_id;
    document.querySelector("#mcForm").appendChild(idInput);

    if (window.MathJax) MathJax.typesetPromise();
  }
});
