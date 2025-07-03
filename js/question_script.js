const $ = id => document.getElementById(id);

// ========== MathJax ==========
function updateMathJax() {
  if (window.MathJax && MathJax.typesetPromise) {
    MathJax.typesetPromise();
  }
}

// ========== Preview ==========
function updatePreview() {
  const question = $("question").value;
  const a = $("answer1").value;
  const b = $("answer2").value;
  const c = $("answer3").value;
  const d = $("answer4").value;
  const correct = $("correct_answer").value;
  const showQ = $("toggle_preview_question").checked;
  const showA = $("toggle_preview_answers").checked;
  const showAll = $("toggle_preview_all").checked;

  let html = "";
  if (showAll || showQ) html += `<div><strong>Câu hỏi:</strong><br>${question}</div><br>`;
  if (showAll || showA) {
    html += `<div><strong>A:</strong> ${a}<br>`;
    html += `<strong>B:</strong> ${b}<br>`;
    html += `<strong>C:</strong> ${c}<br>`;
    html += `<strong>D:</strong> ${d}<br></div><br>`;
  }
  if (showAll) html += `<div><strong>Đáp án đúng:</strong> <span style="color:green;">${correct}</span></div>`;

  $("preview_area").innerHTML = html;
  updateMathJax();
}

["question", "answer1", "answer2", "answer3", "answer4", "correct_answer"].forEach(id => {
  $(id).addEventListener("input", updatePreview);
});
["toggle_preview_question", "toggle_preview_answers", "toggle_preview_all"].forEach(id => {
  $(id).addEventListener("change", updatePreview);
});

// ========== Xử lý ảnh ==========
$("select_image").addEventListener("click", () => $("image_input").click());

$("image_input").addEventListener("change", function () {
  const file = this.files[0];
  if (!file || !file.type.startsWith("image/")) return;

  const reader = new FileReader();
  reader.onload = function (e) {
    $("preview_image").src = e.target.result;
    $("preview_image").style.display = "block";
    $("delete_image").style.display = "inline-block";
  };
  reader.readAsDataURL(file);
});

$("delete_image").addEventListener("click", () => {
  $("image_input").value = "";
  $("image_url").value = "";
  $("preview_image").src = "";
  $("preview_image").style.display = "none";
  $("delete_image").style.display = "none";
  $("delete_image").dataset.delete = "1";
});

// ========== Gửi form ==========
$("questionForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  const id = formData.get("id").trim();
  const hasNewImage = $("image_input").files.length > 0;
  const isUpdate = id !== "";

  // Nếu đang thêm mới thì chờ server trả về ID rồi mới upload ảnh
  if (!isUpdate) {
    fetch("insert_question.php", {
      method: "POST",
      body: formData,
    })
    .then(res => res.json())
    .then(async data => {
      alert(data.message);
      if (data.status === "success") {
        $("question_id").value = data.id;
        if (hasNewImage) {
          await uploadImageToCloudinary(data.id, formData);
        }
        refreshIframe();
      }
    })
    .catch(err => {
      alert("❌ Lỗi khi thêm dữ liệu.");
      console.error(err);
    });
    return;
  }

  // Nếu cập nhật và có ảnh => upload trước rồi gửi form
  if (hasNewImage) {
    const cloudResult = await uploadImageToCloudinary(id, formData);
    if (!cloudResult.success) {
      alert("❌ Lỗi upload ảnh lên Cloudinary.");
      return;
    }
    formData.set("image_url", cloudResult.secure_url);
  }

  if ($("delete_image").dataset.delete === "1") {
    formData.append("delete_image", "1");
  }

  fetch("update_question.php", {
    method: "POST",
    body: formData,
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      if (data.status === "success") {
        if (data.new_image_url) {
          $("image_url").value = data.new_image_url;
          $("preview_image").src = data.new_image_url;
          $("preview_image").style.display = "block";
          $("delete_image").style.display = "inline-block";
        }
        refreshIframe();
        $("delete_image").dataset.delete = "0";
      }
    })
    .catch(err => {
      alert("❌ Lỗi khi cập nhật.");
      console.error(err);
    });
});

// ========== Upload lên Cloudinary ==========
async function uploadImageToCloudinary(id, formData) {
  const uploadData = new FormData();
  uploadData.append("file", $("image_input").files[0]);
  uploadData.append("upload_preset", "ml_default");
  uploadData.append("public_id", `pic_${id}`);

  try {
    const res = await fetch("https://api.cloudinary.com/v1_1/dbdf2gwc9/image/upload", {
      method: "POST",
      body: uploadData,
    });
    const json = await res.json();
    return json.secure_url
      ? { success: true, secure_url: json.secure_url }
      : { success: false };
  } catch (err) {
    return { success: false };
  }
}

// ========== Reset ==========
$("resetBtn").addEventListener("click", () => {
  $("questionForm").reset();
  $("preview_area").innerHTML = "";
  $("preview_image").src = "";
  $("preview_image").style.display = "none";
  $("image_url").value = "";
  $("delete_image").style.display = "none";
  $("delete_image").dataset.delete = "0";

  $("toggle_preview_question").checked = true;
  $("toggle_preview_answers").checked = true;
  $("toggle_preview_all").checked = true;

  updatePreview();
});

// ========== Xoá ==========
$("deleteBtn").addEventListener("click", () => {
  const id = $("question_id").value;
  if (!id || !confirm("Bạn có chắc muốn xoá câu hỏi này?")) return;
  fetch("delete_question.php", {
    method: "POST",
    body: new URLSearchParams({ id }),
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      if (data.status === "success") {
        $("resetBtn").click();
        refreshIframe();
      }
    });
});

// ========== Export PDF ==========
$("exportPdfBtn").addEventListener("click", () => {
  window.open("export_exam_pdf.php", "_blank");
});

// ========== Nhận dữ liệu từ iframe ==========
window.addEventListener("message", event => {
  if (event.origin !== window.location.origin) return;
  const { type, data } = event.data;
  if (type === "fillForm") {
    $("question_id").value = data.id;
    $("topic").value = data.topic;
    $("question").value = data.question;
    $("answer1").value = data.answer1;
    $("answer2").value = data.answer2;
    $("answer3").value = data.answer3;
    $("answer4").value = data.answer4;
    $("correct_answer").value = data.correct_answer;
    $("image_url").value = data.image;

    if (data.image) {
      $("preview_image").src = data.image;
      $("preview_image").style.display = "block";
      $("delete_image").style.display = "inline-block";
    } else {
      $("preview_image").style.display = "none";
      $("delete_image").style.display = "none";
    }

    $("delete_image").dataset.delete = "0";
    updatePreview();
  }
});

// ========== Refresh iframe ==========
function refreshIframe() {
  const iframe = $("questionIframe");
  iframe.src = iframe.src;
}
