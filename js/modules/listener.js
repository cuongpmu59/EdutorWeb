// Nhận dữ liệu từ iframe (mc_table.php) và đổ vào form
window.addEventListener("message", function (event) {
    console.log("Đã nhận được message:", event.data); // ✅ THÊM DÒNG NÀY
    if (event.data?.type !== "mc_selected_row") return;
  
    const d = event.data.data;
  
    // Gán giá trị vào các input
    document.getElementById("mc_id").value = d.mc_id || "";
    document.getElementById("mc_topic").value = d.mc_topic || "";
    document.getElementById("mc_question").value = d.mc_question || "";
    document.getElementById("mc_answer1").value = d.mc_answer1 || "";
    document.getElementById("mc_answer2").value = d.mc_answer2 || "";
    document.getElementById("mc_answer3").value = d.mc_answer3 || "";
    document.getElementById("mc_answer4").value = d.mc_answer4 || "";
    document.getElementById("mc_correct_answer").value = d.mc_correct_answer || "";
  
    // Xử lý ảnh minh hoạ
    const img = document.getElementById("mc_imagePreview");
    if (d.mc_image_url) {
      img.src = d.mc_image_url;
      img.style.display = "block";
    } else {
      img.src = "";
      img.style.display = "none";
    }
  
    // Kéo lên đầu trang để dễ thấy
    window.scrollTo({ top: 0, behavior: "smooth" });
  
    // Kích hoạt MathJax preview nếu có
    if (typeof MathJax !== 'undefined') {
      MathJax.typesetPromise();
    }
  });
  