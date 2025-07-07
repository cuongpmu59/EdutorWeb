const $ = id => document.getElementById(id);

export function initImageSelection() {
  $("select_image_tab").addEventListener("click", () => {
    $("image").click();
  });

  $("image").addEventListener("change", async function () {
    const file = this.files[0];
    if (!file) return;

    const formData = new FormData();
    const tempName = "temp_" + Date.now();
    formData.append("file", file);
    formData.append("upload_preset", CLOUDINARY_UPLOAD_PRESET);
    formData.append("public_id", tempName);

    const res = await fetch(`https://api.cloudinary.com/v1_1/${CLOUDINARY_CLOUD_NAME}/image/upload`, {
      method: "POST",
      body: formData
    });

    const data = await res.json();
    if (data.secure_url) {
      $("image_url").value = data.secure_url;
      $("imageTabPreview").src = data.secure_url;
      $("imageTabPreview").style.display = "block";
      $("preview_image").src = data.secure_url;
      $("preview_image").style.display = "block";
      $("imageTabFileName").textContent = file.name;
      $("delete_image_tab").style.display = "inline-block";
    }
  });
}

export function initImageDeletion() {
  $("delete_image_tab").addEventListener("click", async () => {
    const url = $("image_url").value;
    if (!url) return;

    await fetch("delete_cloudinary_image.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "url=" + encodeURIComponent(url)
    });

    $("image_url").value = "";
    $("imageTabPreview").style.display = "none";
    $("preview_image").style.display = "none";
    $("imageTabFileName").textContent = "";
    $("delete_image_tab").style.display = "none";
  });
}

export async function renameTempImage(oldUrl, newId) {
  const res = await fetch("rename_cloudinary_image.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `old_url=${encodeURIComponent(oldUrl)}&new_name=pic_${newId}`
  });

  const data = await res.json();
  if (data.secure_url) {
    $("image_url").value = data.secure_url;
    $("imageTabPreview").src = data.secure_url;
    $("preview_image").src = data.secure_url;
  }
}
