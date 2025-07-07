export async function submitFormData(formData, isNew, hasTempImage) {
    const url = isNew ? "insert_question.php" : "update_question.php";
  
    const res = await fetch(url, {
      method: "POST",
      body: formData
    });
  
    const result = await res.json();
    alert(result.message || "Đã lưu!");
    return result;
  }
  
  export async function deleteQuestion(id) {
    const res = await fetch("delete_question.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "id=" + id
    });
  
    const result = await res.json();
    alert(result.message || "Đã xoá!");
    return result;
  }
  