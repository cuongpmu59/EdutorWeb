<div class="subtab-container">
  <div class="subtab-button active" data-subtab="sa_form_inner">✍️ Nhập câu hỏi</div>
  <div class="subtab-button" data-subtab="sa_image">🖼️ Ảnh minh hoạ</div>
  <div class="subtab-button" data-subtab="sa_preview">👁️ Xem trước</div>
  <div class="subtab-button" data-subtab="sa_table">📋 Danh sách</div>
</div>

<div class="subtab-content active" id="sa_form_inner">
  <?php require 'pages/sa/sa_form_inner.php'; ?>
</div>
<div class="subtab-content" id="sa_image">
  <?php require 'pages/sa/sa_image.php'; ?>
</div>
<div class="subtab-content" id="sa_preview">
  <?php require 'pages/sa/sa_preview.php'; ?>
</div>
<div class="subtab-content" id="sa_table">
  <?php require 'pages/sa/sa_table.php'; ?>
</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const subtabButtons = document.querySelectorAll(".subtab-button");
    const subtabContents = document.querySelectorAll(".subtab-content");

    subtabButtons.forEach(button => {
      button.addEventListener("click", () => {
        const targetId = button.dataset.subtab;

        subtabButtons.forEach(btn => btn.classList.remove("active"));
        subtabContents.forEach(content => content.classList.remove("active"));

        button.classList.add("active");
        document.getElementById(targetId).classList.add("active");
      });
    });
  });
</script>
