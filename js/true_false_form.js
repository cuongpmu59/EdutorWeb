const urls = [
    "true_false_question_form_inner.php",
    "true_false_image_tab.php",
    "preview_true_false_question.php",
    "get_true_false_questions.php"
  ];
  
  function loadTab(index) {
    document.getElementById("contentFrame").src = urls[index];
  
    const buttons = document.querySelectorAll(".tab-button");
    buttons.forEach((btn, i) => {
      btn.classList.toggle("active", i === index);
    });
  }
  