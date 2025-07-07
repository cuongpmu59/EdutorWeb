const $ = id => document.getElementById(id);

export function populateForm(data) {
  $("question_id").value = data.id;
  $("topic").value = data.topic || "";
  $("question").value = data.question || "";
  $("answer1").value = data.answer1 || "";
  $("answer2").value = data.answer2 || "";
  $("answer3").value = data.answer3 || "";
  $("answer4").value = data.answer4 || "";
  $("correct_answer").value = data.correct_answer || "";
  $("image_url").value = data.image || "";
}

export function initPreviewListeners(updatePreview) {
  ["topic", "question", "answer1", "answer2", "answer3", "answer4"].forEach(id => {
    $(id).addEventListener("input", updatePreview);
  });
}

export function initReset(afterReset = () => {}) {
  $("resetBtn").addEventListener("click", () => {
    $("questionForm").reset();
    $("question_id").value = "";
    $("image_url").value = "";
    afterReset();
  });
}

export function resetForm() {
  $("resetBtn").click();
}

export function initSubmit(onSubmit) {
  $("questionForm").addEventListener("submit", async function (e) {
    e.preventDefault();
    const isNew = !$("question_id").value;
    const hasTempImage = $("image_url").value.includes("temp_");

    const formData = new FormData(this);
    if (isNew && hasTempImage) {
      formData.append("temp_image", $("image_url").value);
    }

    await onSubmit(formData, isNew, hasTempImage);
  });
}

export function initDelete(onDelete) {
  $("deleteBtn").addEventListener("click", async () => {
    const id = $("question_id").value;
    if (id && confirm("Xác nhận xoá câu hỏi này?")) {
      await onDelete(id);
    }
  });
}
