// js/modules/listener.js

window.addEventListener("message", function (event) {
    if (event.data.type === "mc_selected_row") {
      const d = event.data.data;
      document.getElementById("mc_id").value = d.mc_id;
      document.getElementById("mc_topic").value = d.mc_topic;
      document.getElementById("mc_question").value = d.mc_question;
      document.getElementById("mc_answer1").value = d.mc_answer1;
      document.getElementById("mc_answer2").value = d.mc_answer2;
      document.getElementById("mc_answer3").value = d.mc_answer3;
      document.getElementById("mc_answer4").value = d.mc_answer4;
      document.getElementById("mc_correct_answer").value = d.mc_correct_answer;
  
      const img = document.getElementById("mc_imagePreview");
      if (d.mc_image_url) {
        img.src = d.mc_image_url;
        img.style.display = "block";
      } else {
        img.src = "";
        img.style.display = "none";
      }
  
      // Cuộn form lên đầu trang
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  });
  