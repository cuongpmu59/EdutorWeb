const questions = [
  {
    question: "Giá trị của \\( \\int_0^1 x^2 \\, dx \\) là:",
    options: {
      a: "\\( \\frac{1}{2} \\)",
      b: "\\( \\frac{1}{3} \\)",
      c: "\\( 1 \\)"
    },
    answer: "b"
  },
  {
    question: "Nghiệm của phương trình \\( x^2 - 4 = 0 \\) là:",
    options: {
      a: "\\( x = \\pm2 \\)",
      b: "\\( x = 4 \\)",
      c: "\\( x = 0 \\)"
    },
    answer: "a"
  },
  {
    question: "Đạo hàm của hàm số \\( f(x) = x^3 \\) là:",
    options: {
      a: "\\( 3x^2 \\)",
      b: "\\( x^2 \\)",
      c: "\\( 2x \\)"
    },
    answer: "a"
  },
  {
    question: "Giá trị nhỏ nhất của hàm \\( y = x^2 + 2x + 5 \\) là:",
    options: {
      a: "4",
      b: "5",
      c: "6"
    },
    answer: "c"
  },
  {
    question: "Giới hạn của dãy \\( a_n = \\frac{1}{n} \\) khi \\( n \\to \\infty \\) là:",
    options: {
      a: "0",
      b: "1",
      c: "\\( \\infty \\)"
    },
    answer: "a"
  }
];

// RANDOM hóa mảng câu hỏi
function shuffleArray(array) {
  return array.sort(() => Math.random() - 0.5);
}

const selectedQuestions = shuffleArray(questions).slice(0, 4);

const form = document.getElementById("quizForm");
selectedQuestions.forEach((q, i) => {
  const div = document.createElement("div");
  div.classList.add("question");
  div.innerHTML = `
    <p>${i + 1}. ${q.question}</p>
    ${Object.entries(q.options).map(([key, val]) =>
      `<label><input type="radio" name="q${i}" value="${key}"> ${val}</label><br>`
    ).join("")}
  `;
  form.appendChild(div);
});

// Tính điểm
function submitQuiz() {
  let score = 0;
  selectedQuestions.forEach((q, i) => {
    const selected = document.querySelector(`input[name="q${i}"]:checked`);
    if (selected && selected.value === q.answer) score++;
  });
  document.getElementById("result").innerText = `Bạn đúng ${score}/${selectedQuestions.length} câu.`;
}

// Đồng hồ đếm ngược
let time = 600;
const timerEl = document.getElementById("timer");
const interval = setInterval(() => {
  const minutes = Math.floor(time / 60);
  const seconds = time % 60;
  timerEl.textContent = `Thời gian còn lại: ${minutes}:${seconds.toString().padStart(2, "0")}`;
  if (time <= 0) {
    clearInterval(interval);
    submitQuiz();
    alert("Hết thời gian! Bài làm đã được nộp.");
  }
  time--;
}, 1000);
