// Xem trước LaTeX khi click icon/mặc định tự render
document.getElementById('mcTogglePreview').addEventListener('click', () => {
    document.body.classList.toggle('show-preview');
  });
  
  // Có thể tích hợp MathJax hoặc KaTeX
  function renderLatex(){
    ['mc_question','mc_opt_A','mc_opt_B','mc_opt_C','mc_opt_D'].forEach(id => {
      const el = document.getElementById(id);
      // giả sử có KaTeX:
      renderMathInElement(el, {
        delimiters: [ {left: "$$", right: "$$", display: true}, {left: "\\(", right: "\\)", display: false} ]
      });
    });
  }
  
  ['mc_question','mc_opt_A','mc_opt_B','mc_opt_C','mc_opt_D'].forEach(id => {
    document.getElementById(id).addEventListener('input', renderLatex);
  });
  