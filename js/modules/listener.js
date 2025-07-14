window.addEventListener("message", function (event) {
    if (!event.data || event.data.type !== "mc_selected_row") return;
  
    const d = event.data.data;
    if (!d) return;
  
    // Gán dữ liệu lên form
    document.getElementById("mc_id").value = d.mc_id || "";
    document.getElementById("mc_topic").value = d.mc_topic || "";
    document.getElementById("mc_question").value = d.mc_question || "";
    document.getElementById("mc_answer1").value = d.mc_answer1 || "";
    document.getElementById("mc_answer2").value = d.mc_answer2 || "";
    document.getElementById("mc_answer3").value = d.mc_answer3 || "";
    document.getElementById("mc_answer4").value = d.mc_answer4 || "";
    document.getElementById("mc_correct_answer").value = d.mc_correct_answer || "";
  
    // Hiển thị ảnh minh hoạ nếu có
    const img = document.getElementById("mc_imagePreview");
    if (d.mc_image_url) {
      img.src = d.mc_image_url;
      img.style.display = "block";
    } else {
      img.src = "";
      img.style.display = "none";
    }
  
    // Cập nhật xem trước công thức
    const fields = ["mc_question", "mc_answer1", "mc_answer2", "mc_answer3", "mc_answer4"];
    fields.forEach(id => {
      const content = d[id] || "";
      document.getElementById("preview_" + id).innerHTML = content;
    });
  
    if (window.MathJax) MathJax.typeset(); // Hiển thị công thức nếu có MathJax
  });
  