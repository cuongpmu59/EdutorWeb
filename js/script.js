document.addEventListener('DOMContentLoaded', () => {
  const totalQuestions = 2; // ← Thay số lượng câu hỏi tại đây nếu có thêm

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
