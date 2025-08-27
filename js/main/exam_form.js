// === Đồng bộ phiếu trả lời ===
function syncAnswer(idx, opt) {
  const r = document.querySelector(`input[name="s${idx}"][value="${opt}"]`);
  if (r) r.checked = true;
}
function syncQuestion(idx, opt) {
  const r = document.querySelector(`input[name="q${idx}"][value="${opt}"]`);
  if (r) r.checked = true;
}

// === Timer + Progress bar + Âm thanh ===
let duration = 20 * 60; // 20 phút
let interval = null;
let timerStarted = false;

function startTimer() {
  if (timerStarted) return;
  timerStarted = true;

  const countdown = document.getElementById("countdown");
  const progressBar = document.getElementById("progressBar");

  const tickSound = document.getElementById("tickSound");
  const bellSound = document.getElementById("bellSound");

  interval = setInterval(() => {
    duration--;

    let minutes = Math.floor(duration / 60);
    let seconds = duration % 60;
    countdown.textContent = `${minutes}:${seconds.toString().padStart(2, "0")}`;
    progressBar.style.width = `${(duration / (20 * 60)) * 100}%`;

    // Tick khi còn <= 60s
    if (duration <= 60 && duration > 0) {
      tickSound.currentTime = 0;
      tickSound.play().catch(() => {});
    }

    // Hết giờ
    if (duration <= 0) {
      clearInterval(interval);
      bellSound.play().catch(() => {});
      handleSubmit();
    }
  }, 1000);
}

// Chỉ bắt đầu khi user có hành động (tránh chặn autoplay)
document.addEventListener("click", function once() {
  startTimer();
  document.removeEventListener("click", once);
});

// === Xử lý Nộp bài ===
function handleSubmit() {
  clearInterval(interval);

  let total = document.querySelectorAll(".question").length;
  let score = 0;

  document.querySelectorAll(".question").forEach((q, i) => {
    const correct = q.querySelector(`#correct${i}`).value;
    const chosen = q.querySelector(`input[name="q${i}"]:checked`);
    if (chosen && chosen.value === correct) {
      score++;
    }
  });

  document.getElementById("scoreBox").style.display = "block";
  document.getElementById("scoreBox").innerHTML =
    `<h3>Kết quả: ${score}/${total} câu đúng</h3>`;

  document.getElementById("btnSubmit").disabled = true;
  document.getElementById("btnShow").disabled = false;
}

// === Xử lý Xem đáp án ===
function handleShowAnswers() {
  document.querySelectorAll(".question").forEach((q, i) => {
    const correct = q.querySelector(`#correct${i}`).value;
    q.querySelectorAll("input[type=radio]").forEach(r => {
      if (r.value === correct) {
        r.parentElement.style.backgroundColor = "#c8e6c9"; // xanh nhạt
      } else if (r.checked) {
        r.parentElement.style.backgroundColor = "#ffcdd2"; // đỏ nhạt
      }
    });
  });
  document.getElementById("btnShow").disabled = true;
}

// === Xử lý Reset ===
function handleReset() {
  clearInterval(interval);
  duration = 20 * 60;
  timerStarted = false;

  document.querySelectorAll("input[type=radio]").forEach(r => {
    r.checked = false;
    r.parentElement.style.backgroundColor = "";
  });

  document.getElementById("scoreBox").style.display = "none";
  document.getElementById("btnSubmit").disabled = false;
  document.getElementById("btnShow").disabled = true;

  document.getElementById("countdown").textContent = "20:00";
  document.getElementById("progressBar").style.width = "100%";

  // Chờ click để start lại
  document.addEventListener("click", function once() {
    startTimer();
    document.removeEventListener("click", once);
  });
}
