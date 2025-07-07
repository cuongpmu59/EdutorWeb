export function render(id, content) {
    const box = document.getElementById(id);
    box.innerHTML = content;
    MathJax.typesetPromise([box]);
  }
  
  export function renderAll(data) {
    render("questionPreview", data.question);
    render("answer1Preview", data.answer1);
    render("answer2Preview", data.answer2);
    render("answer3Preview", data.answer3);
    render("answer4Preview", data.answer4);
  }
  
  export function initPreviewListeners() {
    const fields = ["question", "answer1", "answer2", "answer3", "answer4"];
    fields.forEach(id => {
      document.getElementById(id).addEventListener("input", () => {
        render(`${id}Preview`, document.getElementById(id).value);
      });
    });
  }
  
  export function clearPreview() {
    ["questionPreview", "answer1Preview", "answer2Preview", "answer3Preview", "answer4Preview"].forEach(id => {
      document.getElementById(id).innerHTML = "";
    });
  }
  