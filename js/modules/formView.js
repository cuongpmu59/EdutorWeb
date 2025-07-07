import * as previewView from './previewView.js';

export function initEvents(onSubmit) {
  document.getElementById("saveBtn").addEventListener("click", () => {
    const formData = getFormData();
    const imageChanged = Boolean(document.getElementById("image").files.length);
    onSubmit(formData, imageChanged);
  });

  document.getElementById("resetBtn").addEventListener("click", clear);
}

export function getFormData() {
  return {
    id: document.getElementById("questionId").value || null,
    topic: document.getElementById("topic").value,
    question: document.getElementById("question").value,
    answers: [
      document.getElementById("answer1").value,
      document.getElementById("answer2").value,
      document.getElementById("answer3").value,
      document.getElementById("answer4").value
    ],
    correct: document.querySelector('input[name="correct"]:checked')?.value,
    image: document.getElementById("imagePreview").src
  };
}

export function populateForm(data) {
  document.getElementById("questionId").value = data.id || '';
  document.getElementById("topic").value = data.topic || '';
  document.getElementById("question").value = data.question || '';
  document.getElementById("answer1").value = data.answer1 || '';
  document.getElementById("answer2").value = data.answer2 || '';
  document.getElementById("answer3").value = data.answer3 || '';
  document.getElementById("answer4").value = data.answer4 || '';
  if (data.correct) {
    document.querySelector(`input[value="${data.correct}"]`).checked = true;
  }
  document.getElementById("imagePreview").src = data.image || '';
  previewView.renderAll(data);
}

export function clear() {
  document.getElementById("questionForm").reset();
  document.getElementById("imagePreview").src = '';
  previewView.clearPreview();
}
