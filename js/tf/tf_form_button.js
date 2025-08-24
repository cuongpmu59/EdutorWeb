// ===============================
// tf_form_button.js
// ===============================

$(document).ready(function () {
  const $form = $("#tfForm");

  // Hàm reset form
  function resetForm() {
    $form[0].reset();
    $("#tf_id").val("");
    $("#tf_preview_image").hide().attr("src", "");
    $("#tf_image_url").val("");
    $(".preview-box").hide().empty();

    for (let i = 1; i <= 4; i++) {
      $(`input[name="tf_correct_answer${i}"]`).prop("checked", false);
    }

    if (typeof updateFullPreview === "function") {
      updateFullPreview();
    }
  }

  // Hàm validate
  function validateForm() {
    const topic = $("#tf_topic").val().trim();
    const question = $("#tf_question").val().trim();
    if (!topic || !question) {
      alert("⚠️ Vui lòng nhập đầy đủ thông tin câu hỏi.");
      return false;
    }

    for (let i = 1; i <= 4; i++) {
      const stmt = $(`#tf_statement${i}`).val().trim();
      const checked = $(`input[name="tf_correct_answer${i}"]:checked`).length;
      if (!stmt || checked === 0) {
        alert(`⚠️ Vui lòng nhập mệnh đề ${i} và chọn Đúng/Sai.`);
        return false;
      }
    }
    return true;
  }

  // ====== Nút Lưu ======
  $("#tf_save").on("click", function (e) {
    e.preventDefault();
    if (!validateForm()) return;

    const formData = new FormData($form[0]);

    $.ajax({
      url: "../../pages/tf/tf_save.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (res) {
        if (res.status === "success") {
          alert("✅ Đã lưu thành công!");
          resetForm();

          const frame = document.getElementById("tfTableFrame");
          if (frame && frame.contentWindow) {
            frame.contentWindow.location.reload();
          }
        } else {
          alert("❌ Lỗi: " + res.message);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        alert("❌ Lỗi khi lưu dữ liệu.");
      }
    });
  });

  // ====== Nút Xóa ======
  $("#tf_delete").on("click", function () {
    const id = $("#tf_id").val();
    if (!id) {
      alert("⚠️ Chưa chọn câu hỏi để xóa.");
      return;
    }
    if (!confirm("Bạn có chắc muốn xóa câu hỏi này?")) return;

    $.ajax({
      url: "../../pages/tf/tf_delete.php",
      type: "POST",
      data: { id },
      dataType: "json",
      success: function (res) {
        if (res.status === "success") {
          alert("🗑️ Đã xóa thành công!");
          resetForm();

          const frame = document.getElementById("tfTableFrame");
          if (frame && frame.contentWindow) {
            frame.contentWindow.location.reload();
          }
        } else {
          alert("❌ Lỗi: " + res.message);
        }
      },
      error: function () {
        alert("❌ Lỗi khi xóa dữ liệu.");
      }
    });
  });

  // ====== Nút Làm mới ======
  $("#tf_reset").on("click", function () {
    resetForm();
  });

  // ====== Nút Ẩn/hiện danh sách ======
  $("#tf_view_list").on("click", function () {
    $("#tfTableWrapper").toggle();
  });

  // ====== Nút Làm đề ======
  $("#tf_preview_exam").on("click", function () {
    window.open("../../pages/tf/tf_preview_exam.php", "_blank");
  });
});
