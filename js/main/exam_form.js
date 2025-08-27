// === Đồng bộ phiếu trả lời ===
function syncAnswer(idx,opt){
  const r = document.querySelector(`input[name="s${idx}"][value="${opt}"]`);
  if (r) r.checked = true;
}
function syncQuestion(idx,opt){
  const r = document.querySelector(`input[name="q${idx}"][value="${opt}"]`);
  if (r) r.checked = true;
}

// === Timer + Progress bar ===
let duration = 20 * 60; // 20 phút
let remaining = duration;
let timer;
let ticking = false;

function formatTime(sec){
  const m = Math.floor(sec/60).toString().padStart(2,'0');
  const s = (sec%60).toString().padStart(2,'0');
  return `${m}:${s}`;
}

function showBanner(msg){
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

  timer = setInterval(()=>{
    remaining--;
    let percent = Math.max(0, Math.round(((duration-remaining)/duration)*100));
    document.getElementById('progressBar').style.width = percent + "%";
    document.getElementById('countdown').textContent = formatTime(remaining);

    // Bắt đầu phát tick khi còn 60 giây
    if(remaining === 60 && !ticking){
      ticking = true;
      if (tickAudio){
        tickAudio.currentTime = 0;
        tickAudio.play().catch(()=>{});
      }
    }

    // Hết giờ
    if(remaining <= 0){
      clearInterval(timer);
      if (bellAudio){
        bellAudio.currentTime = 0;
        bellAudio.play().catch(()=>{});
      }
      handleSubmit(true);
      showBanner("⏰ Hết giờ! Hệ thống đã tự động nộp bài.");
    }
  },1000);
}

// === Nút xử lý ===
function handleSubmit(auto=false){
  document.getElementById('leftCol').classList.add('dim');
  document.getElementById('answerSheet').classList.add('dim');
  document.getElementById('btnShow').disabled = false;
  document.getElementById('btnSubmit').disabled = true;

  // Chấm điểm
  let total = document.querySelectorAll('.question').length;
  let correctCount = 0;
  document.querySelectorAll('.question').forEach((qDiv,idx)=>{
    let correct = document.getElementById('correct'+idx).value;
    let chosen = document.querySelector(`input[name="q${idx}"]:checked`);
    if(chosen && chosen.value===correct){
      correctCount++;
    }
  });
  let score = correctCount + "/" + total + " câu đúng (" + (correctCount*10/total).toFixed(2) + " điểm)";
  document.getElementById('scoreBox').style.display="block";
  document.getElementById('scoreBox').textContent = "✅ Kết quả: " + score;

  if(!auto){
    showBanner("📤 Bạn đã nộp bài thành công!");
  }
}

function markAnswers(radioGroup, correct){
  radioGroup.forEach(r=>{
    if(r.value===correct){
      r.parentElement.classList.add('correct-answer');
    }
    if(r.checked && r.value!==correct){
      r.parentElement.classList.add('wrong-answer');
    }
  });
}

function handleShowAnswers(){
  document.getElementById('leftCol').classList.remove('dim');
  document.getElementById('answerSheet').classList.remove('dim');
  document.querySelectorAll('.question').forEach((qDiv,idx)=>{
    let correct = document.getElementById('correct'+idx).value;
    markAnswers(qDiv.querySelectorAll('input[type=radio]'), correct);
    markAnswers(document.querySelectorAll(`input[name="s${idx}"]`), correct);
  });
  MathJax.typesetPromise();
}

function handleReset(){
  location.reload();
}

// === Auto điều chỉnh layout đáp án ===
// === Auto điều chỉnh layout đáp án ===
function adjustLayout() {
  document.querySelectorAll('.answers').forEach(ans => {
    ans.classList.remove('layout-1','layout-2','layout-3');
    ans.classList.add('layout-1'); // mặc định: 1 dòng 4 cột

    // nếu bị tràn ngang → thử layout-2 (2 dòng 2 cột)
    if (ans.scrollWidth > ans.clientWidth + 5) {
      ans.classList.remove('layout-1');
      ans.classList.add('layout-2');
    }

    // nếu layout-2 vẫn tràn → ép về layout-3 (4 dòng 1 cột)
    if (ans.scrollWidth > ans.clientWidth + 5) {
      ans.classList.remove('layout-2');
      ans.classList.add('layout-3');
    }
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
