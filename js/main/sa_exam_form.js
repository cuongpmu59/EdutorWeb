// === Đồng bộ phiếu trả lời ===
function syncAnswer(idx, val) {
    const sheet = document.getElementById(`sheet${idx}`);
    if (sheet) sheet.value = val;
  }
  function syncQuestion(idx, val) {
    const input = document.getElementById(`input${idx}`);
    if (input) input.value = val;
  }
  
  // === Timer + Progress bar ===
  let duration = 20 * 60; // 20 phút
  let remaining = duration;
  let timer;
  let ticking = false;
  
  function formatTime(sec) {
    const m = Math.floor(sec / 60).toString().padStart(2, '0');
    const s = (sec % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
  }
  
  function showBanner(msg) {
    const box = document.getElementById('msgBox');
    if (!box) return;
    box.textContent = msg;
    box.style.display = "block";
  }
  
  function startTimer() {
    clearInterval(timer);
    remaining = duration;
    document.getElementById('countdown').textContent = formatTime(remaining);
  
    const tickAudio = document.getElementById('tickSound');
    const bellAudio = document.getElementById('bellSound');
  
    timer = setInterval(() => {
      remaining--;
      let percent = Math.max(0, Math.round(((duration - remaining) / duration) * 100));
      document.getElementById('progressBar').style.width = percent + "%";
      document.getElementById('countdown').textContent = formatTime(remaining);
  
      // Bắt đầu phát tick khi còn 60 giây
      if (remaining === 60 && !ticking) {
        ticking = true;
        if (tickAudio) {
          tickAudio.currentTime = 0;
          tickAudio.play().catch(() => { });
        }
      }
  
      // Hết giờ
      if (remaining <= 0) {
        clearInterval(timer);
        if (bellAudio) {
          bellAudio.currentTime = 0;
          bellAudio.play().catch(() => { });
        }
        handleSubmit(true);
        showBanner("⏰ Hết giờ! Hệ thống đã tự động nộp bài.");
      }
    }, 1000);
  }
  
  // === Nút xử lý ===
  function handleSubmit(auto = false) {
    document.getElementById('leftCol').classList.add('dim');
    document.getElementById('answerSheet').classList.add('dim');
    document.getElementById('btnShow').disabled = false;
    document.getElementById('btnSubmit').disabled = true;
  
    // Chấm điểm
    let total = document.querySelectorAll('.question').length;
    let correctCount = 0;
    document.querySelectorAll('.question').forEach((qDiv, idx) => {
      let correct = document.getElementById('correct' + idx).value.trim();
      let answer = document.getElementById('input' + idx).value.trim();
      if (answer === correct) correctCount++;
    });
  
    const percent = ((correctCount / total) * 100).toFixed(2);
    const box = document.getElementById('scoreBox');
    box.style.display = 'block';
    box.textContent = `✅ Kết quả: ${correctCount} / ${total} (${percent}%)`;
  
    if (!auto) {
      showBanner("📤 Bạn đã nộp bài thành công!");
    }
  }
  
  // Xem đáp án
  function handleShowAnswers() {
    document.getElementById('leftCol').classList.remove('dim');
    document.getElementById('answerSheet').classList.remove('dim');
    document.querySelectorAll('.question').forEach((qDiv, idx) => {
      const correct = document.getElementById('correct' + idx).value;
      document.getElementById('input' + idx).value = correct;
      document.getElementById('sheet' + idx).value = correct;
    });
    if (typeof MathJax !== "undefined") {
      MathJax.typesetPromise();
    }
  }
  
  // Reset
  function handleReset() {
    const total = document.querySelectorAll('.question').length;
    for (let i = 0; i < total; i++) {
      document.getElementById('input' + i).value = '';
      document.getElementById('sheet' + i).value = '';
    }
    document.getElementById('scoreBox').style.display = 'none';
    document.getElementById('btnShow').disabled = false;
  }
  
  // === Auto điều chỉnh layout đáp án ===
  function adjustLayout() {
    document.querySelectorAll('.answers').forEach(ans => {
      ans.classList.remove('layout-1', 'layout-2', 'layout-3');
      ans.classList.add('layout-sa'); // short-answer input
    });
  }
  
  document.addEventListener("DOMContentLoaded", () => {
    if (typeof MathJax !== "undefined") {
      MathJax.typesetPromise();
    }
    startTimer();
    adjustLayout();
    window.addEventListener("resize", adjustLayout);
  });
  