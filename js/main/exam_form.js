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
let duration = 1.5 * 60; // 20 phút (bạn có thể chỉnh lại)
let remaining = duration;
let timer;

function formatTime(sec){
  const m = Math.floor(sec/60).toString().padStart(2,'0');
  const s = (sec%60).toString().padStart(2,'0');
  return `${m}:${s}`;
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

    // Chỉ phát tick trong 60 giây cuối
    if(remaining <= 60 && remaining > 0){
      tickAudio.currentTime = 0;
      tickAudio.play().catch(()=>{});
    }

    // Hết giờ
    if(remaining <= 0){
      clearInterval(timer);
      bellAudio.currentTime = 0;
      bellAudio.play().catch(()=>{});
      handleSubmit(true);
      alert("⏰ Hết giờ! Hệ thống đã tự động nộp bài.");
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
    alert("📤 Bạn đã nộp bài thành công!");
  }
}

function handleShowAnswers(){
  document.getElementById('leftCol').classList.remove('dim');
  document.getElementById('answerSheet').classList.remove('dim');
  document.querySelectorAll('.question').forEach((qDiv,idx)=>{
    let correct = document.getElementById('correct'+idx).value;
    let radios = qDiv.querySelectorAll('input[type=radio]');
    radios.forEach(r=>{
      if(r.value===correct){
        r.parentElement.classList.add('correct-answer');
      }
      if(r.checked && r.value!==correct){
        r.parentElement.classList.add('wrong-answer');
      }
    });
    let sheetRadios = document.querySelectorAll(`input[name="s${idx}"]`);
    sheetRadios.forEach(r=>{
      if(r.value===correct){
        r.parentElement.classList.add('correct-answer');
      }
      if(r.checked && r.value!==correct){
        r.parentElement.classList.add('wrong-answer');
      }
    });
  });
  MathJax.typesetPromise();
}

function handleReset(){
  location.reload();
}

// === Auto điều chỉnh layout đáp án ===
function adjustLayout() {
  document.querySelectorAll('.answers').forEach(ans => {
    ans.classList.remove('layout-1','layout-2','layout-3');
    ans.classList.add('layout-1'); // mặc định: 1 dòng 4 cột

    // nếu bị tràn → thử layout-2
    if (ans.scrollHeight > ans.clientHeight + 5) {
      ans.classList.remove('layout-1');
      ans.classList.add('layout-2');
    }

    // nếu layout-2 vẫn tràn → ép về layout-3
    if (ans.scrollHeight > ans.clientHeight + 5) {
      ans.classList.remove('layout-2');
      ans.classList.add('layout-3');
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  MathJax.typesetPromise();
  startTimer();
  adjustLayout();           
  window.addEventListener("resize", adjustLayout);
});
