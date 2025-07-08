// =======================
// File: pages/tf/tf_form.php
// =======================
?>
<div class="subtab-container">
  <div class="subtab-button active" data-subtab="tf_form_inner">✅ Nhập câu hỏi</div>
  <div class="subtab-button" data-subtab="tf_image">🖼️ Ảnh minh hoạ</div>
  <div class="subtab-button" data-subtab="tf_preview">👁️ Xem trước</div>
  <div class="subtab-button" data-subtab="tf_table">📋 Danh sách</div>
</div>

<div class="subtab-content active" id="tf_form_inner"><?php require 'pages/tf/tf_form_inner.php'; ?></div>
<div class="subtab-content" id="tf_image"><?php require 'pages/tf/tf_image.php'; ?></div>
<div class="subtab-content" id="tf_preview"><?php require 'pages/tf/tf_preview.php'; ?></div>
<div class="subtab-content" id="tf_table"><?php require 'pages/tf/tf_table.php'; ?></div>

<script>
  document.querySelectorAll(".subtab-button").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".subtab-button").forEach(b => b.classList.remove("active"));
      document.querySelectorAll(".subtab-content").forEach(c => c.classList.remove("active"));
      btn.classList.add("active");
      document.getElementById(btn.dataset.subtab).classList.add("active");
    });
  });
</script>
<?php
