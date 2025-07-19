function updatePreviews() {
  const fields = ["mc_question", "mc_answer1", "mc_answer2", "mc_answer3", "mc_answer4"];
  fields.forEach(id => {
    const input = document.getElementById(id);
    const preview = document.getElementById(`preview_${id}`);
    preview.innerHTML = input.value;
    MathJax.typesetPromise([preview]);
  });
}

// Optional: export để dùng khi dùng module
window.updatePreviews = updatePreviews;
