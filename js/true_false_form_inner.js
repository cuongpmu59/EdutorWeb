document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("questionForm");
  
    // Khi người dùng thay đổi bất kỳ trường nào → lưu vào localStorage
    form.querySelectorAll("input[type=text], textarea, input[type=radio]").forEach(input => {
      input.addEventListener("input", saveToLocalStorage);
      input.addEventListener("change", saveToLocalStorage);
    });
  
    // Nút gửi form
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      submitQuestion();
    });
  
    // Load lại nếu có sẵn
    loadFromLocalStorage();
  });
  
  function saveToLocalStorage() {
    const data = {
      topic: document.getElementById("topic").value,
      main_question: document.getElementById("main_question").value,
      statement1: document.getElementById("statement1").value,
      correct_answer1: getRadioValue("correct_answer1"),
      statement2: document.getElementById("statement2").value,
      correct_answer2: getRadioValue("correct_answer2"),
      statement3: document.getElementById("statement3").value,
      correct_answer3: getRadioValue("correct_answer3"),
      statement4: document.getElementById("statement4").value,
      correct_answer4: getRadioValue("correct_answer4"),
      image_url: localStorage.getItem("tf_image_url") || ""
    };
    localStorage.setItem("tf_question_data", JSON.stringify(data));
  }
  
  function loadFromLocalStorage() {
    const data = JSON.parse(localStorage.getItem("tf_question_data") || "{}");
    if (!data) return;
    document.getElementById("topic").value = data.topic || "";
    document.getElementById("main_question").value = data.main_question || "";
  
    document.getElementById("statement1").value = data.statement1 || "";
    checkRadio("correct_answer1", data.correct_answer1);
    document.getElementById("statement2").value = data.statement2 || "";
    checkRadio("correct_answer2", data.correct_answer2);
    document.getElementById("statement3").value = data.statement3 || "";
    checkRadio("correct_answer3", data.correct_answer3);
    document.getElementById("statement4").value = data.statement4 || "";
    checkRadio("correct_answer4", data.correct_answer4);
  }
  
  function getRadioValue(name) {
    const selected = document.querySelector(`input[name=${name}]:checked`);
    return selected ? selected.value : "";
  }
  
  function checkRadio(name, value) {
    const radio = document.querySelector(`input[name=${name}][value="${value}"]`);
    if (radio) radio.checked = true;
  }
  
  function submitQuestion() {
    const data = JSON.parse(localStorage.getItem("tf_question_data") || "{}");
  
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "insert_true_false_question.php";
  
    for (const key in data) {
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = key;
      input.value = data[key];
      form.appendChild(input);
    }
  
    document.body.appendChild(form);
    form.submit();
  }
  