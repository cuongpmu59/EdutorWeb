document.getElementById("quizForm").addEventListener("change", function (e) {
  if (e.target.name.startsWith("q")) {
    const questionNumber = e.target.name.substring(1);
    const answerValue = e.target.value;
    const answerSpan = document.getElementById("answer" + questionNumber);
    if (answerSpan) {
      answerSpan.textContent = answerValue;
    }
  }
});

document.getElementById("quizForm").addEventListener("submit", function (e) {
  e.preventDefault();
  alert("Bài làm đã được nộp!");
});
