let totalTime = 600; // 10 phút = 600 giây
let timer;

function startTimer() {
  const countdownEl = document.getElementById("countdown");
  timer = setInterval(() => {
    if (totalTime <= 0) {
      clearInterval(timer);
      submitQuiz();
      countdownEl.textContent = "Hết giờ!";
      countdownEl.style.color = "red";
    } else {
      const minutes = Math.floor(totalTime / 60);
      const seconds = totalTime % 60;

      // Hiển thị dạng 2 chữ số: 09:05
      countdownEl.textContent = `⏰ ${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

      // Đổi màu đỏ khi còn dưới 1 phút
      if (totalTime <= 60) {
        countdownEl.style.color = 'red';
        countdownEl.style.transition = 'color 0.5s ease';
        countdownEl.style.animation = 'blink 1s infinite';
      } else {
        countdownEl.style.animation = ''; // reset nếu quay lại
      }

      totalTime--;
    }
  }, 1000);
}

function updateProgressBar() {
  const totalQuestions = document.querySelectorAll('.question').length;
  const answered = document.querySelectorAll('.question input[type="radio"]:checked').length;
  const percent = Math.round((answered / totalQuestions) * 100);

  const progressBar = document.getElementById('progressBar');
  progressBar.style.width = `${percent}%`;
  progressBar.textContent = `${percent}%`;

  // Thay đổi màu theo mức độ hoàn thành
  if (percent < 40) {
    progressBar.style.backgroundColor = '#f44336';
  } else if (percent < 80) {
    progressBar.style.backgroundColor = '#ff9800';
  } else {
    progressBar.style.backgroundColor = '#4caf50';
  }
}



function resetQuiz() {
  quizSubmitted = false;

  // ✅ Hiển thị lại form
  const form = document.getElementById("quizForm");
  form.style.display = "block";
  form.style.pointerEvents = "auto";
  form.style.opacity = "1";

  // ✅ Xóa kết quả cũ
  document.getElementById("result").innerHTML = '';

  // ✅ Bỏ chọn và mở lại các radio
  document.querySelectorAll('input[type="radio"]').forEach(r => {
    r.checked = false;
    r.disabled = false;
  });

  // ✅ Ẩn nút làm lại
  document.getElementById('retryBtn').style.display = 'none';
}

    
    let quizSubmitted = false;

    function submitQuiz() {
      if (quizSubmitted) return;
      quizSubmitted = true;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    
      // Khóa nút "Nộp bài"
      const submitBtn = document.querySelector('button[onclick*="submitQuiz"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.style.opacity = "0.5"; // làm mờ
        submitBtn.style.cursor = "not-allowed";
      }
          
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
          } else {
            // Nếu không chọn đáp án nào, vẫn tô đáp án đúng
            const correctRadio = questionDiv.querySelector(`input[value="${correct}"]`);
            if (correctRadio) {
              correctRadio.parentElement.style.backgroundColor = '#d4edda';
              correctRadio.parentElement.style.border = '1px solid #28a745';
              correctRadio.parentElement.style.borderRadius = '4px';
              correctRadio.parentElement.style.padding = '2px 4px';
            }
            questionDiv.classList.add("incorrect");
          }
        }
      });
    
      const resultBox = document.getElementById("result");
      let percent = (score / total) * 100;
      resultBox.innerText = `Bạn đúng ${score}/${total} câu (${percent.toFixed(1)}%).`;
      if (percent >= 90) {

        resultBox.innerText += " 🏆 Xuất sắc!";
      } else if (percent >= 75) {
        resultBox.innerText += " 👍 Khá giỏi!";
      } else if (percent >= 50) {
        resultBox.innerText += " 🧐 Trung bình.";
      } else {
        resultBox.innerText += " ❗Cần cố gắng hơn.";
      }

      resultBox.style.padding = "10px";
      resultBox.style.backgroundColor = "#dff0d8";
      resultBox.style.border = "1px solid #3c763d";
      resultBox.style.color = "#3c763d";
      resultBox.style.marginTop = "10px";

      // ✅ Hiện nút Làm lại
      document.querySelectorAll('input[type="radio"]').forEach(r => r.disabled = true);
      document.getElementById('retryBtn').style.display = 'inline-block';

      // Ẩn form sau khi nộp
      document.getElementById("quizForm").style.opacity = "0.3";
      document.getElementById("quizForm").style.pointerEvents = "none";

    }
    
  document.addEventListener('DOMContentLoaded', () => {
    updateProgressBar(); // gọi lúc đầu để khởi tạo

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
      updateProgressBar(); // 👈 THÊM DÒNG NÀY
    });
  });

  // Đồng bộ: từ phiếu → form
  document.querySelectorAll('.answer-sheet input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
      const q = radio.name.replace('as_', '');
      const val = radio.value;
      const quizRadio = document.querySelector(`input[name="${q}"][value="${val}"]`);
      if (quizRadio) quizRadio.checked = true;
      updateProgressBar(); // 👈 THÊM DÒNG NÀY
    });
  });

  // Gọi lại MathJax để hiển thị công thức toán
  if (window.MathJax) {
    MathJax.typesetPromise();
  }
  
});
