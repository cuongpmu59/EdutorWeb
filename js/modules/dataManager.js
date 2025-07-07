export async function saveQuestion(data) {
    const res = await fetch(data.id ? "update_question.php" : "insert_question.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });
    return await res.json(); // { success: true, id: ... }
  }
  
  export async function updateImageURL(id, imageUrl) {
    return fetch("update_image_url.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id, image: imageUrl })
    });
  }
  