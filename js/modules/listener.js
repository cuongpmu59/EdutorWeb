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
  
      const imgPreview = document.getElementById("mc_imagePreview");
      if (d.mc_image_url) {
        imgPreview.src = d.mc_image_url;
        imgPreview.style.display = "block";
        document.getElementById("mc_old_image").value = d.mc_image_url;
      } else {
        imgPreview.style.display = "none";
        imgPreview.src = "";
        document.getElementById("mc_old_image").value = "";
      }
  
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  });
  