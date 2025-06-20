let totalTime = 600; // 10 phút = 600 giây
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
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
    let quizSubmitted = false;
function submitQuiz() {
    if (quizSubmitted) return;
    quizSubmitted = true;
    let score = 0;
    let total = 0;

  const questions = document.querySelectorAll('.question');
  questions.forEach((questionDiv) => {
    const qname = questionDiv.dataset.q;
    const correct = questionDiv.dataset.correct;
    const selected = document.querySelector(`input[name="${qname}"]:checked`);

    questionDiv.classList.remove("correct", "incorrect");

    if (correct) {
      total++;

      if (selected) {
        if (selected.value === correct) {
          score++;
          questionDiv.classList.add("correct");
        } else {
          questionDiv.classList.add("incorrect");

          // Tô màu đáp án đúng
          const correctRadio = questionDiv.querySelector(`input[value="${correct}"]`);
          if (correctRadio) {
            correctRadio.parentElement.style.backgroundColor = '#d4edda';
            correctRadio.parentElement.style.border = '1px solid #28a745';
            correctRadio.parentElement.style.borderRadius = '4px';
            correctRadio.parentElement.style.padding = '2px 4px';
          }
        }
      }
    }
  });

  const resultBox = document.getElementById("result");
  resultBox.innerText = `Bạn đúng ${score}/${total} câu.`;
  resultBox.style.padding = "10px";
  resultBox.style.backgroundColor = "#dff0d8";
  resultBox.style.border = "1px solid #3c763d";
  resultBox.style.color = "#3c763d";
  resultBox.style.marginTop = "10px";
}

document.addEventListener('DOMContentLoaded', () => {
  // Lấy tên học sinh từ prompt
  const student = prompt("Nhập họ tên học sinh:");
  document.getElementById("studentName").textContent = student || "Chưa nhập";

  // Gán các thông tin mặc định
  document.getElementById("studentID").textContent = "HS12345";
  document.getElementById("studentClass").textContent = "12A1";
  document.getElementById("startTime").textContent = new Date().toLocaleTimeString('vi-VN');

  // Bắt đầu đếm giờ
  startTimer();

  // Tạo phiếu trả lời
  const answerSheet = document.querySelector('.answer-sheet');
  const questions = document.querySelectorAll('.question');

  questions.forEach((questionDiv, index) => {
    const qId = questionDiv.dataset.q;
    const row = document.createElement('div');
    row.className = 'answer-row';
    row.dataset.q = qId;

    const span = document.createElement('span');
    span.textContent = `Câu ${index + 1}:`;
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
  });

  // Đồng bộ: từ form → phiếu
  document.querySelectorAll('.question input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
      const q = radio.name;
      const val = radio.value;
      const sheetRadio = document.querySelector(`input[name="as_${q}"][value="${val}"]`);
      if (sheetRadio) sheetRadio.checked = true;
    });
  });

  // Đồng bộ: từ phiếu → form
  document.querySelectorAll('.answer-sheet input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
      const q = radio.name.replace('as_', '');
      const val = radio.value;
      const quizRadio = document.querySelector(`input[name="${q}"][value="${val}"]`);
      if (quizRadio) quizRadio.checked = true;
    });
  });

  // Gọi lại MathJax để hiển thị công thức toán
  if (window.MathJax) {
    MathJax.typeset();
  }
});
