document.getElementById('quizForm').addEventListener('submit', function(event) {
  event.preventDefault();  // Ngăn không cho form được gửi đi

  // Lấy các giá trị đã chọn từ các câu hỏi
  const q1Answer = document.querySelector('input[name="q1"]:checked');
  const q2Answer = document.querySelector('input[name="q2"]:checked');

  // Cập nhật phiếu trả lời
  if (q1Answer) {
    document.getElementById('answer1').textContent = q1Answer.nextSibling.textContent.trim();
  } else {
    document.getElementById('answer1').textContent = 'Chưa chọn';
  }

  if (q2Answer) {
    document.getElementById('answer2').textContent = q2Answer.nextSibling.textContent.trim();
  } else {
    document.getElementById('answer2').textContent = 'Chưa chọn';
  }

  // Nếu có thêm câu hỏi, tiếp tục cập nhật tương tự
});

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
