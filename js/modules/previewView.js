const $ = id => document.getElementById(id);

export function updatePreview() {
  const content = `
    <strong>Chủ đề:</strong> ${$("topic").value}<br>
    <strong>Câu hỏi:</strong><br> ${$("question").value}<br>
    <strong>Đáp án:</strong><br>
    A. ${$("answer1").value}<br>
    B. ${$("answer2").value}<br>
    C. ${$("answer3").value}<br>
    D. ${$("answer4").value}
  `;
  $("preview_area").innerHTML = content;
  MathJax.typesetPromise();
}

export function showImageTab(imageUrl) {
  const imageTab = $("imageTabPreview");
  const preview = $("preview_image");

  if (imageUrl) {
    $("image_url").value = imageUrl;
    imageTab.src = imageUrl;
    imageTab.style.display = "block";
    $("imageTabFileName").textContent = "Đã có ảnh";
    $("delete_image_tab").style.display = "inline-block";

    preview.src = imageUrl;
    preview.style.display = "block";
  } else {
    clearImagePreview();
  }
}

export function clearImagePreview() {
  $("imageTabPreview").style.display = "none";
  $("imageTabFileName").textContent = "";
  $("delete_image_tab").style.display = "none";
  $("preview_image").style.display = "none";
}
