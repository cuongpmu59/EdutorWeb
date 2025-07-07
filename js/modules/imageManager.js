export async function uploadImage(file) {
    const formData = new FormData();
    formData.append("file", file);
    formData.append("upload_preset", "YOUR_PRESET");
  
    const res = await fetch("https://api.cloudinary.com/v1_1/YOUR_CLOUD_NAME/image/upload", {
      method: "POST",
      body: formData
    });
  
    const data = await res.json();
    return data.secure_url;
  }
  
  export async function renameImage(oldUrl, newPublicId) {
    return fetch("rename_image.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ oldUrl, newPublicId })
    });
  }
  
  export async function deleteImage(publicId) {
    return fetch("delete_cloudinary_image.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ publicId })
    });
  }
  