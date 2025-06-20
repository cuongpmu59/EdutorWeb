let totalTime = 600; // 10 phút (600 giây)
let timer;

function startTimer() {
  const countdownEl = document.getElementById("countdown");
  timer = setInterval(() => {
    if (totalTime <= 0) {
      clearInterval(timer);
      submitQuiz();
      countdownEl.textContent = "Hết giờ!";
    } else {
      const minutes = Math.floor(totalTime / 60);
      const seconds = totalTime % 60;
      countdownEl.textContent = `${minutes} phút ${seconds < 10 ? '0' : ''}${seconds} giây`;
      totalTime--;
    }
  }, 1000);
}

function submitQuiz() {
  const answers = {
    q1: "b",
    q2: "b",
    q3: "a",
    q4: "b",
    q5: "c"
  };

  let score = 0;
  for (let key in answers) {
    const questionDiv = document.querySelector(`.question[data-q="${key}"]`);
    const selected = document.querySelector(`input[name="${key}"]:checked`);

    // Xóa màu cũ nếu có
    questionDiv.classList.remove("correct", "incorrect");

    if (selected) {
      if (selected.value === answers[key]) {
        score++;
        questionDiv.classList.add("correct");
      } else {
        questionDiv.classList.add("incorrect");

        // 👉 THÊM đoạn này để làm nổi bật đáp án đúng:
        const correctAnswer = answers[key];
        const correctRadio = questionDiv.querySelector(`input[name="${key}"][value="${correctAnswer}"]`);
        if (correctRadio) {
          correctRadio.parentElement.style.backgroundColor = '#d4edda'; // nền xanh nhạt
          correctRadio.parentElement.style.border = '1px solid #28a745';
          correctRadio.parentElement.style.borderRadius = '4px';
          correctRadio.parentElement.style.padding = '2px 4px';
        }
      }
    }
  }

  document.getElementById("result").innerText = `Bạn đúng ${score}/${Object.keys(answers).length} câu.`;
}

document.addEventListener('DOMContentLoaded', () => {
  const student = prompt("Nhập họ tên học sinh:");
  document.getElementById("studentID").textContent = "HS12345";
  document.getElementById("studentName").textContent = student || "Nguyễn Văn A";
  document.getElementById("studentClass").textContent = "12A1";


  document.getElementById("startTime").textContent = new Date().toLocaleTimeString();

  const now = new Date();
  const startTimeStr = now.toLocaleTimeString('vi-VN');
  document.getElementById("startTime").textContent = startTimeStr;
  startTimer();

  // Tạo các dòng phiếu trả lời
  const answerSheet = document.querySelector('.answer-sheet');
  const questionCount = 50; // chỉnh theo số câu thật
  for (let i = 1; i <= questionCount; i++) {
    const qId = `q${i}`;
    const row = document.createElement('div');
    row.className = 'answer-row';
    row.dataset.q = qId;

    const span = document.createElement('span');
    span.textContent = `Câu ${i}:`;
    row.appendChild(span);

    ['a', 'b', 'c', 'd'].forEach(opt => {
      const label = document.createElement('label');
      const input = document.createElement('input');
      input.type = 'radio';
      input.name = `as_${qId}`;
      input.value = opt;
      label.appendChild(input);
      label.append(` ${opt.toUpperCase()}`);
      row.appendChild(label);
    });

    answerSheet.appendChild(row);
  }

  // Đồng bộ 2 chiều
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
  
  if (window.MathJax) {
    MathJax.typeset();
  }
});


