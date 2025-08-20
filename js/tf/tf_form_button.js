// js/tf/tf_form_button.js
$(document).ready(function () {
  const form = $("#tfForm");

  // ===== Validate form =====
  function validateForm() {
    let valid = true;

    // reset lỗi trước đó
    form.find("input, textarea").removeClass("error");

    // 1. Chủ đề
    if (!$("#tf_topic").val().trim()) {
      $("#tf_topic").addClass("error");
      valid = false;
    }

    // 2. Câu hỏi chính
    if (!$("#tf_question").val().trim()) {
      $("#tf_question").addClass("error");
      valid = false;
    }

    // 3. 4 mệnh đề + đáp án đúng/sai
    for (let i = 1; i <= 4; i++) {
      const stm = $(`#tf_statement${i}`);
      const radios = $(`input[name="correct_answer${i}"]`);

      if (!stm.val().trim()) {
        stm.addClass("error");
        valid = false;
      }

      if (!radios.is(":checked")) {
        radios.closest(".tf-radio-group").addClass("error");
        valid = false;
      } else {
        radios.closest(".tf-radio-group").removeClass("error");
      }
    }

    if (!valid) {
      alert("Vui lòng nhập đầy đủ tất cả các trường!");
    }

    return valid;
  }

  // ===== Nút Lưu =====
  $("#tf_save").on("click", function (e) {
    e.preventDefault();
    if (!validateForm()) return;

    const formData = new FormData(form[0]);

    $.ajax({
      url: "../../includes/tf/tf_form_save.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (res) {
        try {
          const data = JSON.parse(res);
          alert(data.message || "Lưu thành công!");
          if (data.status === "success") {
            $("#tf_reset").click(); // reset sau khi lưu
            document.getElementById("tfTableFrame").contentWindow.location.reload();
          }
        } catch (err) {
          alert("Có lỗi khi xử lý phản hồi từ server!");
        }
      },
      error: function () {
        alert("Không thể kết nối server!");
      },
    });
  });

  // ===== Nút Xóa =====
  $("#tf_delete").on("click", function () {
    const id = $("#tf_id").val();
    if (!id) {
      alert("Chưa chọn câu hỏi để xóa!");
      return;
    }
    if (!confirm("Bạn có chắc chắn muốn xóa?")) return;

    $.post("../../includes/tf/tf_delete.php", { tf_id: id }, function (res) {
      try {
        const data = JSON.parse(res);
        alert(data.message || "Đã xóa!");
        if (data.status === "success") {
          $("#tf_reset").click();
          document.getElementById("tfTableFrame").contentWindow.location.reload();
        }
      } catch {
        alert("Lỗi khi xóa!");
      }
    });
  });

  // ===== Nút Làm mới =====
  $("#tf_reset").on("click", function () {
    form[0].reset();
    form.find("input, textarea").removeClass("error");
    $(".preview-box").hide().text("");
    $("#tf_preview_image").hide().attr("src", "");
    $("#tf_image_url").val("");
  });

  // ===== Nút Ẩn/Hiện danh sách =====
  $("#tf_view_list").on("click", function () {
    $("#tfTableWrapper").slideToggle(200);
  });

  // ===== Nút Làm đề =====
  $("#tf_preview_exam").on("click", function () {
    window.open("../../pages/tf/tf_exam_preview.php", "_blank");
  });
});
