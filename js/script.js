document.addEventListener('DOMContentLoaded', () => {
  const totalQuestions = 31; // ← Thay số lượng câu hỏi tại đây nếu có thêm

  for (let i = 1; i <= totalQuestions; i++) {
    const quizRadios = document.querySelectorAll(`input[name="q${i}"]`);
    const answerRadios = document.querySelectorAll(`input[name="q${i}-answer"]`);
    const answerSpan = document.getElementById(`answer${i}`);

    // Khi chọn ở phần bài thi → cập nhật bên phiếu trả lời và span
    quizRadios.forEach((radio) => {
      radio.addEventListener('change', () => {
        answerRadios.forEach((r) => {
          r.checked = (r.value === radio.value);
        });
        if (answerSpan) answerSpan.textContent = radio.value;
      });
    });

    // Khi chọn ở phiếu trả lời → cập nhật bên bài thi và span
    answerRadios.forEach((radio) => {
      radio.addEventListener('change', () => {
        quizRadios.forEach((r) => {
          r.checked = (r.value === radio.value);
        });
        if (answerSpan) answerSpan.textContent = radio.value;
      });
    });
  }

  // Xử lý khi nộp bài
  document.getElementById("quizForm").addEventListener("submit", function (e) {
    e.preventDefault();
    alert("Bài làm đã được nộp!");
  });
});

const correctAnswers = {
  q1: "B",
  q2: "B",
  q3: "C",
  q4: "D",
  q5: "A",
  q6: "B",
  q7: "D",
  q8: "C",
  q9: "A",
  q10: "B",
  q11: "C",
  q12: "D",
  q13: "B",
  q14: "C",
  q15: "A",
  q16: "B",
  q17: "D",
  q18: "A",
  q19: "C",
  q20: "B",
  q21: "A",
  q22: "B",
  q23: "D",
  q24: "C",
  q25: "A",
  q26: "B",
  q27: "D",
  q28: "C",
  q29: "B",
  q30: "A"
};


document.getElementById('quizForm').addEventListener('submit', function(e) {
  e.preventDefault(); // Ngăn reload trang

  let score = 0;
  let total = Object.keys(correctAnswers).length;

  for (let key in correctAnswers) {
    let selected = document.querySelector(`input[name="${key}"]:checked`);
    if (selected && selected.value === correctAnswers[key]) {
      score++;
    }
  }

  alert(`Bạn đã trả lời đúng ${score}/${total} câu`);
});
