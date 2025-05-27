function submitQuiz() {
    const answers = {
      q1: "b",
      q2: "b",
      q3: "a"
    };
  
    let score = 0;
    for (let key in answers) {
      const selected = document.querySelector(`input[name="${key}"]:checked`);
      if (selected && selected.value === answers[key]) score++;
    }
  
    document.getElementById("result").innerText = `Bạn đúng ${score}/3 câu.`;
  }
  
  // Đồng bộ phiếu trả lời với câu hỏi
  document.querySelectorAll('.question input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
      const q = radio.name;
      const val = radio.value;
      const sheetRadio = document.querySelector(`input[name="as_${q}"][value="${val}"]`);
      if (sheetRadio) sheetRadio.checked = true;
    });
  });
  
  document.querySelectorAll('.answer-sheet input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
      const q = radio.name.replace('as_', '');
      const val = radio.value;
      const quizRadio = document.querySelector(`input[name="${q}"][value="${val}"]`);
      if (quizRadio) quizRadio.checked = true;
    });
  });
  